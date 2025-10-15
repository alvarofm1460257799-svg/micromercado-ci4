<?php

namespace App\Controllers;

use App\Models\VariantesProductoModel;
use App\Models\ProductosModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class VariantesProducto extends BaseController
{
    protected $variantes;
    protected $productos;
    protected $reglas;

    public function __construct()
    {
        $this->variantes = new VariantesProductoModel();
        $this->productos = new ProductosModel();

        $this->reglas = [
            'id_producto' => ['rules' => 'required|numeric', 'errors' => ['required' => 'Debe seleccionar un producto']],
            'codigo_barra' => ['rules' => 'required|max_length[20]', 'errors' => ['required' => 'El código de barra es obligatorio']],
            'descripcion' => ['rules' => 'required|max_length[50]', 'errors' => ['required' => 'La descripción es obligatoria']],
        ];
    }

    // Listado de variantes con nombre de producto
public function index()
{
    $datos = $this->variantes->obtenerConProducto();

    $data = [
        'titulo' => 'Listado de Variantes de Productos',
        'datos' => $datos
    ];

    echo view('header');
    echo view('variantesproducto/Variantesproducto', $data);
    echo view('footer');
}



    // Formulario nuevo
    public function nuevo()
    {
        $productos = $this->productos->where('activo', 1)->findAll();
        $data = ['titulo' => 'Agregar Variante', 'productos' => $productos];

        echo view('header');
        echo view('variantesproducto/nuevo', $data);
        echo view('footer');
    }

  public function insertar()
{
    if ($this->request->getMethod() != "post" || !$this->validate($this->reglas)) {
        $productos = $this->productos->where('activo', 1)->findAll();
        $data = [
            'titulo' => 'Agregar Variante',
            'productos' => $productos,
            'validation' => $this->validator
        ];
        echo view('header');
        echo view('variantesproducto/nuevo', $data);
        echo view('footer');
        return;
    }

    $id_producto = $this->request->getPost('id_producto');
    $codigo_barra = $this->request->getPost('codigo_barra');
    $descripcion = $this->request->getPost('descripcion');

    $variantesModel = new \App\Models\VariantesProductoModel();

    // Buscar cualquier variante con ese código
    $varianteExistente = $variantesModel->where('codigo_barra', $codigo_barra)->first();

    if ($varianteExistente) {
        if ($varianteExistente['activo'] == 1) {
            // Variante activa → no permitir duplicado
            return redirect()->back()->withInput()->with('error', 'Ya existe una variante activa con ese código de barra.');
        } else {
            // Variante inactiva → reactivar
            $variantesModel->update($varianteExistente['id'], [
                'id_producto' => $id_producto,
                'descripcion' => $descripcion,
                'activo' => 1,
                'fecha_edit' => date('Y-m-d H:i:s')
            ]);

            return redirect()->to(base_url('variantesproducto'))->with('mensaje', 'Variante reactivada correctamente.');
        }
    }

    // Insertar nueva variante (solo si no existe)
    $variantesModel->insert([
        'id_producto' => $id_producto,
        'codigo_barra' => $codigo_barra,
        'descripcion' => $descripcion,
        'activo' => 1,
        'fecha_alta' => date('Y-m-d H:i:s')
    ]);

    return redirect()->to(base_url('variantesproducto'))->with('mensaje', 'Variante agregada correctamente.');
}


    // Actualizar variante
    public function actualizar()
    {
        $id = $this->request->getPost('id');
        $codigo_barra = $this->request->getPost('codigo_barra');

        // Verificar duplicado en otras variantes activas
        $duplicado = $this->variantes
                          ->where('codigo_barra', $codigo_barra)
                          ->where('activo', 1)
                          ->where('id !=', $id)
                          ->first();

        if ($duplicado) {
            return redirect()->back()->with('error', 'Ya existe otra variante activa con este código de barra.');
        }

        if($this->validate($this->reglas)){
            $this->variantes->update($id, [
                'id_producto'  => $this->request->getPost('id_producto'),
                'codigo_barra' => $codigo_barra,
                'descripcion'  => $this->request->getPost('descripcion')
            ]);

            return redirect()->to(base_url().'/variantesproducto')->with('mensaje', 'Variante actualizada correctamente.');
        } else {
            return $this->editar($id, $this->validator);
        }
    }

    // Eliminar (desactivar)
    public function eliminar($id)
    {
        $this->variantes->update($id, ['activo' => 0]);
        return redirect()->to(base_url().'/variantesproducto')->with('mensaje', 'Variante eliminada correctamente.');
    }

    // Reingresar variante
    public function reingresar($id)
    {
        $this->variantes->update($id, ['activo' => 1]);
        return redirect()->to(base_url().'/variantesproducto')->with('mensaje', 'Variante reingresada correctamente.');
    }

    public function importar()
    {
        $data['titulo'] = 'Importar Variantes desde Excel';
        echo view('header');
        echo view('variantesproducto/importar', $data);
        echo view('footer');
    }

    public function procesarImportacion()
{
    $archivo = $this->request->getFile('archivo_excel');

    if (!$archivo->isValid()) {
        return redirect()->back()->with('mensaje', 'Error al subir el archivo.');
    }

    try {
        $spreadsheet = IOFactory::load($archivo->getTempName());
        $hoja = $spreadsheet->getActiveSheet();
        $filas = $hoja->toArray(null, true, true, true);

        $productoModel = new ProductosModel();
        $variantesModel = new VariantesProductoModel();

        $errores = [];
        $insertados = 0;

        // 🔹 Saltamos la primera fila (cabecera)
        foreach (array_slice($filas, 1) as $fila) {

            // 📊 Orden de columnas: A = código, B = producto base, C = descripción
            $codigoBarra = trim($fila['A']);
            $nombreProducto = trim($fila['B']);
            $descripcion = trim($fila['C']);

            if ($codigoBarra == '' || $nombreProducto == '' || $descripcion == '') {
                $errores[] = "Fila incompleta (faltan datos): " . json_encode($fila);
                continue;
            }

            // 🔍 Buscar producto por nombre
            $producto = $productoModel->where('nombre', $nombreProducto)->first();
            if (!$producto) {
                $errores[] = "Producto no encontrado: <b>$nombreProducto</b>";
                continue;
            }

            // 🚫 Verificar duplicado de código de barra
            $existe = $variantesModel->where('codigo_barra', $codigoBarra)->first();
            if ($existe) {
                $errores[] = "Código de barra duplicado: <b>$codigoBarra</b>";
                continue;
            }

            // ✅ Insertar variante
            $variantesModel->insert([
                'id_producto' => $producto['id'],
                'codigo_barra' => $codigoBarra,
                'descripcion' => $descripcion,
                'activo' => 1
            ]);

            $insertados++;
        }

        // 📋 Mensaje final
        $mensaje = "Importación completada. <br> Variantes insertadas: <b>$insertados</b>.";
        if (count($errores) > 0) {
            $mensaje .= "<br><br><b>Errores encontrados:</b><ul>";
            foreach ($errores as $e) {
                $mensaje .= "<li>$e</li>";
            }
            $mensaje .= "</ul>";
        }

        return redirect()->to(base_url('/variantesproducto/importar'))->with('mensaje', $mensaje);

    } catch (\Exception $e) {
        return redirect()->back()->with('mensaje', 'Error procesando el archivo: ' . $e->getMessage());
    }
}


 
}
