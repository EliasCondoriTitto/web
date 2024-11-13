<?php

// Include the main TCPDF library (search for installation path).
require_once 'TCPDF-main/tcpdf.php';

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Nombre');
$pdf->SetTitle('Factura de Compra');
$pdf->SetSubject('Factura de Compra');
$pdf->SetKeywords('compra, factura, pdf');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set default monospaced font
$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

// set auto page breaks
$pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// add a page
$pdf->AddPage();

// Configuración de fuente
$pdf->setFont('helvetica', '', 12);

// Agregar el logo de la empresa
$pdf->Image(
    'img/logo_f.jpg', // Ruta de la imagen
    15,               // Coordenada x
    10,               // Coordenada y
    33,               // Ancho de la imagen
    33,               // Alto de la imagen
    '',               // Tipo de la imagen
    '',               // Enlace (ninguno en este caso)
    'T',              // Comportamiento al borde
    false,            // Ajustar a celda (no)
    300,              // Resolución en DPI
    '',               // Padding (ninguno en este caso)
    false,            // Visibilidad
    false,            // Mantener proporción
    0,                // Escala
    false,            // Pintar bordes
    false,            // Llenar fondo
    false             // Rotar imagen
);


// Establece la fuente: Helvetica, Negrita, Tamaño 12 
$pdf->SetFont('helvetica', 'B', 12);

// Información de la empresa
$pdf->SetXY(60, 25);
$pdf->Cell(0, 10, '', 0, 1);
$pdf->SetXY(60, 10);
$pdf->Cell(0, 10, 'ARTESANIAS BOLIVIA', 0, 1, 'C');


// Establece la fuente: Times, Cursiva, Tamaño 14 
$pdf->SetFont('times', 'I', 14); 
// Establece la posición x = 60 e y = 25
$pdf->SetXY(60, 15);
$pdf->Cell(0, 10, '"Conectando Tradición y Modernidad, Artesanias de Corazón a tu Hogar"', 0, 1, 'C');

$pdf->SetXY(60, 20);
$pdf->Cell(0, 10, '', 0, 1);  //Teléfono: (555) 555-5555
$pdf->SetXY(60, 25);
$pdf->Cell(0, 10, '', 0, 1);   // Email: contacto@empresa.com

// Línea separadora
$pdf->Ln(20);
$pdf->Line(10, 45, 200, 45);

// Título de la factura
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Factura de Compra', 0, 1, 'C');
$pdf->Ln(5);

// Datos del cliente y la factura
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, 'Cliente: Raquel Apaza', 0, 1);
$pdf->Cell(0, 10, 'Fecha: ' . date('d/m/Y'), 0, 1);
$pdf->Cell(0, 10, 'Número de Factura: 123456', 0, 1);
$pdf->Ln(10);

// Encabezado de la tabla de productos
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(60, 10, 'Descripción', 1, 0, 'C');
$pdf->Cell(30, 10, 'Cantidad', 1, 0, 'C');
$pdf->Cell(40, 10, 'Precio Unitario', 1, 0, 'C');
$pdf->Cell(40, 10, 'Total', 1, 1, 'C');

// Detalle de productos (ejemplo)
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(60, 10, 'Aguayo', 1, 0, 'L');
$pdf->Cell(30, 10, '1', 1, 0, 'C');
$pdf->Cell(40, 10, 'BOB 100.00', 1, 0, 'R');
$pdf->Cell(40, 10, 'BOB 100.00', 1, 1, 'R');

$pdf->Cell(60, 10, 'Poncho', 1, 0, 'L');
$pdf->Cell(30, 10, '1', 1, 0, 'C');
$pdf->Cell(40, 10, 'BOB 120.00', 1, 0, 'R');
$pdf->Cell(40, 10, 'BOB 120.00', 1, 1, 'R');

// Total de la factura
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(130, 10, 'Sub Total:', 1, 0, 'R');
$pdf->Cell(40, 10, 'BOB 220.00', 1, 1, 'R');

$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(130, 10, 'Costo de envio:', 1, 0, 'R');
$pdf->Cell(40, 10, 'BOB 2.47', 1, 1, 'R');

$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(130, 10, 'Total:', 1, 0, 'R');
$pdf->Cell(40, 10, 'BOB 222.47', 1, 1, 'R');

// Añadir un espacio antes del QR
$pdf->Ln(15);

// Muestra el QR de pago
$pdf->Image('img/qr_code.png', 15, $pdf->GetY(), 50, 50, '', '', 'T', false, 300, '', false, false, 0, false, false, false);

//Close and output PDF document
$pdf->Output('factura_compra.pdf', 'I');
?>
