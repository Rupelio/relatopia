<?php

// Criar uma imagem de 32x32 pixels
$image = imagecreatetruecolor(32, 32);

// Tornar o fundo transparente
imagealphablending($image, false);
imagesavealpha($image, true);
$transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
imagefill($image, 0, 0, $transparent);
imagealphablending($image, true);

// Definir as cores
$pink = imagecolorallocate($image, 236, 72, 153);     // #ec4899
$blue = imagecolorallocate($image, 59, 130, 246);     // #3b82f6
$green = imagecolorallocate($image, 16, 185, 129);    // #10b981
$gray = imagecolorallocate($image, 107, 114, 128);    // #6b7280

// Desenhar bonequinho da esquerda (rosa)
// Cabeça
imagefilledellipse($image, 10, 8, 6, 6, $pink);
// Corpo
imagefilledellipse($image, 10, 18, 8, 12, $pink);

// Desenhar bonequinho da direita (azul)
// Cabeça
imagefilledellipse($image, 22, 8, 6, 6, $blue);
// Corpo
imagefilledellipse($image, 22, 18, 8, 12, $blue);

// Desenhar coração no centro (verde)
$heart_points = array(
    16, 12,  // ponto central
    14, 10,  // esquerda superior
    12, 12,  // esquerda
    14, 16,  // esquerda inferior
    16, 18,  // ponto inferior
    18, 16,  // direita inferior
    20, 12,  // direita
    18, 10   // direita superior
);
imagefilledpolygon($image, $heart_points, 8, $green);

// Desenhar conexão entre os braços
imagefilledellipse($image, 16, 12, 2, 4, $gray);

// Salvar como PNG primeiro
imagepng($image, 'favicon.png');

// Tentar criar múltiplos tamanhos para ICO
$sizes = [16, 32, 48];
$images = [];

foreach ($sizes as $size) {
    $resized = imagecreatetruecolor($size, $size);
    imagealphablending($resized, false);
    imagesavealpha($resized, true);
    $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
    imagefill($resized, 0, 0, $transparent);

    imagecopyresampled($resized, $image, 0, 0, 0, 0, $size, $size, 32, 32);
    $images[] = $resized;
}

// Limpar memória
imagedestroy($image);

echo "Favicon PNG criado com sucesso!\n";
echo "Para converter para ICO, use um conversor online ou:\n";
echo "- Visite https://www.icoconverter.com/\n";
echo "- Faça upload do favicon.png\n";
echo "- Baixe o favicon.ico\n";

?>
