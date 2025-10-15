<?php

namespace App\Controllers;

use App\Models\PresentacionesModel;
use App\Models\ProductosModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
class Presentaciones extends BaseController
{
    protected $presentaciones;
    protected $productos;

    public function __construct()
    {
        $this->presentaciones = new PresentacionesModel();
        $this->productos = new ProductosModel();


        helper(['form']);
        $this->reglas = [
    'codigo' => [
        'rules' => 'required|is_unique[presentaciones_productos.codigo]',
        'errors' => [
            'required' => 'El campo {field} es obligatorio.',
            'is_unique' => 'El {field} ya está registrado. Debe ser único.'
        ]
    ],
    'tipo' => [
        'rules' => 'required|max_length[20]',
        'errors' => [
            'required' => 'El campo {field} es obligatorio.',
            'max_length' => 'El campo {field} no debe exceder los 20 caracteres.'
        ]
    ],
    'cantidad_unidades' => [
        'rules' => 'required|integer|greater_than[0]',
        'errors' => [
            'required' => 'El campo Cantidad x Presentación es obligatorio.',
            'integer' => 'Debe ser un número entero.',
            'greater_than' => 'Debe ser mayor que cero.'
        ]
    ],
    'precio_compra' => [
        'rules' => 'required|decimal',
        'errors' => [
            'required' => 'El campo Precio Compra es obligatorio.',
            'decimal' => 'El campo Precio Compra debe ser un número decimal.'
        ]
    ],
    'precio_venta' => [
        'rules' => 'required|decimal|greater_than[0]',
        'errors' => [
            'required' => 'El campo Precio Venta es obligatorio.',
            'decimal' => 'Debe ser un número decimal.',
            'greater_than' => 'Debe ser mayor que cero.'
        ]
    ]
];

    }

public function index()
{
    $data['presentaciones'] = $this->presentaciones
        ->select('presentaciones_productos.*, productos.nombre AS nombre_producto')
        ->join('productos', 'productos.id = presentaciones_productos.id_producto')
        ->where('presentaciones_productos.activo', 1) // Solo activas
        ->orderBy('presentaciones_productos.id', 'DESC') // Más recientes primero
        ->findAll();

    echo view('header');
    echo view('presentaciones/index', $data);
    echo view('footer');
}
// Controlador:

    public function nuevo()
    {
        $productos = $this->productos->findAll();

        $db = \Config\Database::connect();
        $presentaciones_padre = $db->table('presentaciones_productos p')
            ->select('p.id, p.tipo, p.cantidad_unidades, pr.nombre as nombre_producto')
            ->join('productos pr', 'p.id_producto = pr.id')
            ->where('p.activo', 1)
            ->get()
            ->getResultArray();

        $data = [
            'productos' => $productos,
            'presentaciones_padre' => $presentaciones_padre,
        ];

        echo view('header');
        echo view('presentaciones/nuevo', $data);
        echo view('footer');
    }

    public function insertar()
    {
        if ($this->request->getMethod() == 'post') {
            if (!$this->validate($this->reglas)) {
                return redirect()->back()->withInput()->with('validation', $this->validator);
            }

            $this->presentaciones->save([
                'id_producto' => $this->request->getPost('id_producto'),
                'tipo' => $this->request->getPost('tipo'),
                'codigo' => $this->request->getPost('codigo'),
                'cantidad_unidades' => $this->request->getPost('cantidad_unidades'),
                'precio_venta' => $this->request->getPost('precio_venta'),
                'precio_compra' => $this->request->getPost('precio_compra'),
                'id_padre' => $this->request->getPost('id_padre') ?: null,
                'activo' => 1,
            ]);

            return redirect()->to(base_url('presentaciones'))->with('mensaje', 'Presentación creada correctamente.');
        }

        // Si no es POST, simplemente redirigir a nuevo formulario
        return redirect()->to(base_url('presentaciones/nuevo'));
    }



public function editar($id, $validation = null)
{
    $presentacion = $this->presentaciones->find($id);

    if (!$presentacion) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Presentación con ID $id no encontrada.");
    }

    $productos = $this->productos->where('activo', 1)->findAll();

    $db = \Config\Database::connect();
    $presentaciones_padre = $db->table('presentaciones_productos p')
        ->select('p.id, p.tipo, p.cantidad_unidades, pr.nombre as nombre_producto')
        ->join('productos pr', 'p.id_producto = pr.id')
        ->where('p.activo', 1)
        ->get()
        ->getResultArray();

    $data = [
        'presentacion' => $presentacion,
        'productos' => $productos,
        'presentaciones_padre' => $presentaciones_padre
    ];

    if ($validation !== null) {
        $data['validation'] = $validation;
    }

    echo view('header');
    echo view('presentaciones/editar', $data);
    echo view('footer');
}


public function actualizar()
{
    $id = $this->request->getPost('id');
    $presentacion = $this->presentaciones->find($id);

    if (!$presentacion) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Presentación con ID $id no encontrada.");
    }

    // Clonamos las reglas
    $reglas = $this->reglas;

    // Si no cambió el código, quitamos is_unique para evitar error falso
    if ($this->request->getPost('codigo') === $presentacion['codigo']) {
        $reglas['codigo']['rules'] = 'required';
    }

    // Validar datos
    if (!$this->validate($reglas)) {
        // Volvemos al formulario con errores y datos
        $db = \Config\Database::connect();
        $presentaciones_padre = $db->table('presentaciones_productos p')
            ->select('p.id, p.tipo, p.cantidad_unidades, pr.nombre as nombre_producto')
            ->join('productos pr', 'p.id_producto = pr.id')
            ->where('p.activo', 1)
            ->get()
            ->getResultArray();

        $data = [
            'validation' => $this->validator,
            'productos' => $this->productos->where('activo', 1)->findAll(),
            'presentacion' => $this->request->getPost(), // para mantener lo que ya escribió el usuario
            'presentaciones_padre' => $presentaciones_padre
        ];

        echo view('header');
        echo view('presentaciones/editar', $data);
        echo view('footer');
        return;
    }

    // Si pasa la validación, actualizamos
    $this->presentaciones->update($id, [
        'id_producto' => $this->request->getPost('id_producto'),
        'tipo' => $this->request->getPost('tipo'),
        'cantidad_unidades' => $this->request->getPost('cantidad_unidades'),
        'precio_venta' => $this->request->getPost('precio_venta'),
        'precio_compra' => $this->request->getPost('precio_compra'),
        'codigo' => $this->request->getPost('codigo'),
        'id_padre' => $this->request->getPost('id_padre') ?: null
    ]);

    return redirect()->to(base_url('presentaciones'))->with('mensaje', 'Presentación actualizada correctamente.');
}





    public function eliminar($id)
{
    $this->presentaciones->update($id, ['activo' => 0]);
    return redirect()->to(base_url('presentaciones'))->with('mensaje', 'Presentación eliminada');
}


public function eliminados()
{
    $data = [
        'titulo' => 'Presentaciones Eliminadas',
        'presentaciones' => $this->presentaciones
            ->select('presentaciones_productos.*, productos.nombre AS nombre_producto')
            ->join('productos', 'productos.id = presentaciones_productos.id_producto')
            ->where('presentaciones_productos.activo', 0)
            ->orderBy('presentaciones_productos.id', 'DESC')
            ->findAll()
    ];

    echo view('header');
    echo view('presentaciones/eliminados', $data);
    echo view('footer');
}


 public function reingresar($id)
{
    $this->presentaciones->update($id, ['activo' => 1]);
    return redirect()->to(base_url('presentaciones/eliminados'))->with('mensaje', 'Presentación reingresada');
}



public function generarUnidades()
{
    $db = \Config\Database::connect();

    // Obtener todos los productos activos
    $productos = $db->table('productos')->where('activo', 1)->get()->getResultArray();

    foreach ($productos as $producto) {
        // Verificar si ya existe una presentación tipo "unidad" con cantidad = 1
        $existe = $db->table('presentaciones_productos')
            ->where('id_producto', $producto['id'])
            ->where('tipo', 'unidad')
            ->where('cantidad_unidades', 1)
            ->countAllResults();

        if ($existe == 0) {
            // Insertar presentación tipo "unidad"
            $db->table('presentaciones_productos')->insert([
                'id_producto' => $producto['id'],
                'codigo' => $producto['codigo'],

                'tipo' => 'unidad',
                'cantidad_unidades' => 1,
                'precio_venta' => $producto['precio_venta'],   // opcional
                'precio_compra' => $producto['precio_compra'], // opcional
                'id_padre' => null,
                'activo' => 1
            ]);
        }
    }

    return redirect()->to(base_url('presentaciones'))->with('mensaje', 'Presentaciones "unidad" generadas con éxito.');
}


public function autocompletar() {
    $term = $this->request->getGet('term');

    if (!$term) {
        return $this->response->setJSON([]);
    }

    $resultados = $this->presentaciones
        ->select('presentaciones_productos.id AS id_presentacion, 
                  presentaciones_productos.id_producto, 
                  productos.nombre AS nombre_producto,
                  presentaciones_productos.tipo, 
                  presentaciones_productos.codigo')
        ->join('productos', 'productos.id = presentaciones_productos.id_producto')
        ->groupStart()
            ->like('productos.nombre', $term)
            ->orLike('presentaciones_productos.codigo', $term)
            ->orLike('presentaciones_productos.tipo', $term)
        ->groupEnd()
        ->where('presentaciones_productos.activo', 1)
        ->findAll(10);

    return $this->response->setJSON($resultados);
}


public function importarVista()
{
    echo view('header');
    echo view('presentaciones/importar');
    echo view('footer');
}
public function importarExcelPresentaciones()
{
    $archivo = $this->request->getFile('archivo_excel');

    if (!$archivo->isValid()) {
        return redirect()->back()->with('mensaje', '<div class="alert alert-danger">Error al subir el archivo.</div>');
    }

    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $spreadsheet = $reader->load($archivo->getTempName());
    $hoja = $spreadsheet->getActiveSheet();
    $filas = $hoja->toArray(null, true, true, true);

    $presentacionesModel = new \App\Models\PresentacionesModel();
    $productosModel = new \App\Models\ProductosModel();

    $datos_importados = [];
    $errores = [];
    $duplicados = [];
    $insertados = 0;

    // --- 1️⃣ Leer filas del Excel ---
    foreach ($filas as $fila_num => $fila) {
        if ($fila_num == 1) continue; // Saltar cabecera

        $codigo = trim($fila['A']);
        $nombre_producto = trim($fila['B']);
        $tipo = trim($fila['C']);
        $cantidad_unidades = (int)$fila['D'];
        $precio_compra = (float)$fila['E'];
        $precio_venta = (float)$fila['F'];

        if (!$codigo || !$nombre_producto) {
            $errores[] = "Fila $fila_num: Código o producto vacío.";
            continue;
        }

        $producto = $productosModel->where('nombre', $nombre_producto)->first();
        if (!$producto) {
            $errores[] = "Fila $fila_num: Producto '$nombre_producto' no encontrado.";
            continue;
        }

        $yaExiste = $presentacionesModel->where('codigo', $codigo)->first();
        if ($yaExiste) {
            $duplicados[] = "Fila $fila_num: Código duplicado '$codigo' — no insertado.";
            continue;
        }

        $datos_importados[] = [
            'codigo' => $codigo,
            'id_producto' => $producto['id'],
            'tipo' => $tipo,
            'cantidad_unidades' => $cantidad_unidades,
            'precio_compra' => $precio_compra,
            'precio_venta' => $precio_venta,
        ];
    }

    // --- 2️⃣ Insertar todas las presentaciones ---
    foreach ($datos_importados as $fila) {
        try {
            $presentacionesModel->insert([
                'id_producto' => $fila['id_producto'],
                'codigo' => $fila['codigo'],
                'tipo' => $fila['tipo'],
                'cantidad_unidades' => $fila['cantidad_unidades'],
                'precio_compra' => $fila['precio_compra'],
                'precio_venta' => $fila['precio_venta'],
                'id_padre' => null,
                'activo' => 1
            ]);
            $insertados++;
        } catch (\Exception $e) {
            $errores[] = "Error al insertar código '{$fila['codigo']}': " . $e->getMessage();
        }
    }

    // --- 3️⃣ Conectar jerarquías automáticamente ---
    try {
        // Agrupar por producto
        $productosConPresentaciones = $presentacionesModel
            ->select('id_producto')
            ->distinct()
            ->findAll();

        foreach ($productosConPresentaciones as $p) {
            $id_producto = $p['id_producto'];

            // Obtener todas las presentaciones del producto
            $presentaciones = $presentacionesModel
                ->where('id_producto', $id_producto)
                ->orderBy('cantidad_unidades', 'ASC')
                ->findAll();

            // Conectar jerárquicamente
            for ($i = 0; $i < count($presentaciones) - 1; $i++) {
                $hijo = $presentaciones[$i];
                $padre = $presentaciones[$i + 1];

                // Solo conectar si el padre tiene más unidades
                if ($padre['cantidad_unidades'] > $hijo['cantidad_unidades']) {
                    $presentacionesModel->update($hijo['id'], ['id_padre' => $padre['id']]);
                }
            }
        }
    } catch (\Exception $e) {
        $errores[] = "Error al conectar jerarquías: " . $e->getMessage();
    }

    // --- 4️⃣ Mensaje final ---
    $mensaje = "<div class='alert alert-success'>✅ Importación completada. Total insertados: <b>$insertados</b></div>";

    if (!empty($duplicados)) {
        $mensaje .= "<div class='alert alert-info'><b>Códigos duplicados (no insertados):</b><br>" . implode('<br>', $duplicados) . "</div>";
    }

    if (!empty($errores)) {
        $mensaje .= "<div class='alert alert-warning'><b>Errores/Advertencias:</b><br>" . implode('<br>', $errores) . "</div>";
    }

    return redirect()->to(base_url('presentaciones/importarVista'))->with('mensaje', $mensaje);
}



}
