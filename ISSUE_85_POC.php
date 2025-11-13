<?php
/**
 * Proof of Concept: Text-to-Path Conversion for Issue #85
 *
 * This demonstrates how to implement text-to-path conversion for SVG generation.
 *
 * APPROACH OPTIONS:
 *
 * Option 1: Use meyfa/php-svg library (most promising)
 * Option 2: Use external command (Inkscape CLI)
 * Option 3: Use pre-generated path mappings
 *
 * This POC shows Option 3 (simplest, no dependencies) for demonstration purposes.
 * A production implementation would likely use Option 1 or 2.
 */

class TextToPathConverter
{
    /**
     * Pre-generated SVG path data for uppercase letters and numbers
     * These are simplified paths based on a sans-serif font
     *
     * In a real implementation, these would be:
     * - Generated from an actual font file using a library
     * - Scaled and positioned based on font size
     * - Support both regular and bold weights
     *
     * Coordinates are relative to a 1000x1000 unit square (typical font em-square)
     */
    private static $glyphPaths = [
        'A' => 'M 100 700 L 300 100 L 500 100 L 700 700 L 600 700 L 550 550 L 250 550 L 200 700 Z M 280 450 L 520 450 L 400 180 Z',
        'B' => 'M 100 700 L 100 100 L 400 100 Q 550 100 550 220 Q 550 300 480 330 Q 600 360 600 480 Q 600 700 400 700 Z M 200 620 L 380 620 Q 500 620 500 480 Q 500 380 380 380 L 200 380 Z M 200 300 L 380 300 Q 450 300 450 220 Q 450 180 380 180 L 200 180 Z',
        'C' => 'M 700 600 Q 650 680 500 680 Q 300 680 300 400 Q 300 120 500 120 Q 650 120 700 200 L 600 250 Q 570 180 500 180 Q 380 180 380 400 Q 380 620 500 620 Q 570 620 600 550 Z',
        'D' => 'M 100 700 L 100 100 L 400 100 Q 700 100 700 400 Q 700 700 400 700 Z M 200 620 L 400 620 Q 620 620 620 400 Q 620 180 400 180 L 200 180 Z',
        'E' => 'M 100 700 L 100 100 L 650 100 L 650 180 L 200 180 L 200 350 L 600 350 L 600 430 L 200 430 L 200 620 L 650 620 L 650 700 Z',
        'F' => 'M 100 700 L 100 100 L 650 100 L 650 180 L 200 180 L 200 350 L 600 350 L 600 430 L 200 430 L 200 700 Z',
        'G' => 'M 700 600 Q 650 680 500 680 Q 300 680 300 400 Q 300 120 500 120 Q 650 120 700 200 L 600 250 Q 570 180 500 180 Q 380 180 380 400 Q 380 620 500 620 Q 570 620 600 550 L 600 450 L 480 450 L 480 370 L 680 370 L 680 650 Z',
        'H' => 'M 100 700 L 100 100 L 200 100 L 200 350 L 600 350 L 600 100 L 700 100 L 700 700 L 600 700 L 600 430 L 200 430 L 200 700 Z',
        'I' => 'M 300 700 L 300 100 L 500 100 L 500 700 Z',
        'J' => 'M 500 700 L 500 250 Q 500 120 350 120 Q 250 120 200 170 L 150 100 Q 230 30 350 30 Q 600 30 600 250 L 600 700 Z',
        'K' => 'M 100 700 L 100 100 L 200 100 L 200 350 L 550 100 L 680 100 L 350 380 L 700 700 L 570 700 L 280 450 L 200 520 L 200 700 Z',
        'L' => 'M 100 700 L 100 100 L 200 100 L 200 620 L 650 620 L 650 700 Z',
        'M' => 'M 100 700 L 100 100 L 250 100 L 400 550 L 550 100 L 700 100 L 700 700 L 600 700 L 600 250 L 450 700 L 350 700 L 200 250 L 200 700 Z',
        'N' => 'M 100 700 L 100 100 L 220 100 L 580 550 L 580 100 L 680 100 L 680 700 L 560 700 L 200 250 L 200 700 Z',
        'O' => 'M 400 120 Q 200 120 200 400 Q 200 680 400 680 Q 600 680 600 400 Q 600 120 400 120 Z M 400 180 Q 520 180 520 400 Q 520 620 400 620 Q 280 620 280 400 Q 280 180 400 180 Z',
        'P' => 'M 100 700 L 100 100 L 450 100 Q 650 100 650 280 Q 650 450 450 450 L 200 450 L 200 700 Z M 200 370 L 450 370 Q 550 370 550 280 Q 550 180 450 180 L 200 180 Z',
        'Q' => 'M 400 120 Q 200 120 200 400 Q 200 680 400 680 Q 600 680 600 400 Q 600 220 500 160 L 580 70 L 650 120 L 550 230 Q 680 300 680 400 Q 680 750 400 750 Q 120 750 120 400 Q 120 50 400 50 Z M 400 180 Q 520 180 520 400 Q 520 620 400 620 Q 280 620 280 400 Q 280 180 400 180 Z',
        'R' => 'M 100 700 L 100 100 L 450 100 Q 650 100 650 280 Q 650 400 520 440 L 680 700 L 550 700 L 400 450 L 200 450 L 200 700 Z M 200 370 L 450 370 Q 550 370 550 280 Q 550 180 450 180 L 200 180 Z',
        'S' => 'M 650 180 Q 600 120 450 120 Q 280 120 280 220 Q 280 320 420 360 L 480 375 Q 700 440 700 580 Q 700 680 550 680 Q 350 680 280 550 L 370 500 Q 420 600 550 600 Q 620 600 620 560 Q 620 500 480 460 L 420 445 Q 200 380 200 220 Q 200 30 450 30 Q 650 30 720 140 Z',
        'T' => 'M 100 700 L 100 620 L 350 620 L 350 100 L 450 100 L 450 620 L 700 620 L 700 700 Z',
        'U' => 'M 100 700 L 100 250 Q 100 100 400 100 Q 700 100 700 250 L 700 700 L 600 700 L 600 250 Q 600 180 400 180 Q 200 180 200 250 L 200 700 Z',
        'V' => 'M 50 700 L 350 100 L 450 100 L 750 700 L 650 700 L 400 200 L 150 700 Z',
        'W' => 'M 50 700 L 200 100 L 300 100 L 400 550 L 500 100 L 600 100 L 750 700 L 650 700 L 550 200 L 450 650 L 350 650 L 250 200 L 150 700 Z',
        'X' => 'M 100 700 L 300 400 L 100 100 L 220 100 L 400 350 L 580 100 L 700 100 L 500 400 L 700 700 L 580 700 L 400 450 L 220 700 Z',
        'Y' => 'M 100 700 L 350 380 L 350 100 L 450 100 L 450 380 L 700 700 L 580 700 L 400 480 L 220 700 Z',
        'Z' => 'M 100 700 L 100 620 L 580 180 L 100 180 L 100 100 L 700 100 L 700 180 L 220 620 L 700 620 L 700 700 Z',
        '0' => 'M 400 120 Q 200 120 200 400 Q 200 680 400 680 Q 600 680 600 400 Q 600 120 400 120 Z M 400 180 Q 520 180 520 400 Q 520 620 400 620 Q 280 620 280 400 Q 280 180 400 180 Z',
        '1' => 'M 350 650 L 250 550 L 300 500 L 350 550 L 350 100 L 450 100 L 450 700 L 350 700 Z',
        '2' => 'M 200 200 Q 250 120 400 120 Q 600 120 600 300 Q 600 450 400 500 L 200 620 L 200 700 L 700 700 L 700 620 L 350 620 L 480 530 Q 680 430 680 280 Q 680 30 400 30 Q 200 30 120 150 Z',
        '3' => 'M 200 200 Q 250 120 400 120 Q 600 120 600 260 Q 600 350 500 380 Q 600 410 600 520 Q 600 680 400 680 Q 250 680 200 600 L 280 550 Q 310 600 400 600 Q 520 600 520 520 Q 520 440 380 440 L 350 440 L 350 360 L 380 360 Q 520 360 520 260 Q 520 180 400 180 Q 310 180 280 230 Z',
        '4' => 'M 500 700 L 150 300 L 150 220 L 500 220 L 500 100 L 600 100 L 600 220 L 700 220 L 700 300 L 600 300 L 600 700 Z M 500 300 L 250 300 L 500 580 Z',
        '5' => 'M 650 700 L 200 700 L 200 400 L 420 400 Q 650 400 650 550 Q 650 680 450 680 Q 300 680 250 600 L 170 650 Q 250 750 450 750 Q 730 750 730 550 Q 730 330 450 330 L 280 330 L 280 620 L 650 620 Z',
        '6' => 'M 500 680 Q 350 680 300 600 L 220 650 Q 300 750 500 750 Q 700 750 700 600 L 700 580 Q 650 680 500 680 Z M 400 600 Q 620 600 620 480 Q 620 360 400 360 Q 180 360 180 480 Q 180 600 400 600 Z M 400 430 Q 260 430 260 480 Q 260 530 400 530 Q 540 530 540 480 Q 540 430 400 430 Z M 400 290 Q 700 290 700 480 Q 700 650 500 680 L 500 600 Q 620 580 620 480 Q 620 360 400 360 Q 180 360 180 480 Q 180 670 400 670 Q 500 670 550 620 L 620 670 Q 550 750 400 750 Q 100 750 100 480 Q 100 220 400 220 Z',
        '7' => 'M 100 700 L 100 620 L 580 620 L 300 100 L 400 100 L 700 620 L 700 700 Z',
        '8' => 'M 400 120 Q 600 120 600 250 Q 600 340 500 380 Q 600 420 600 530 Q 600 680 400 680 Q 200 680 200 530 Q 200 420 300 380 Q 200 340 200 250 Q 200 120 400 120 Z M 400 180 Q 280 180 280 250 Q 280 320 400 360 Q 520 320 520 250 Q 520 180 400 180 Z M 400 440 Q 280 480 280 530 Q 280 620 400 620 Q 520 620 520 530 Q 520 480 400 440 Z',
        '9' => 'M 400 120 Q 200 120 200 200 L 200 220 Q 250 120 400 120 Z M 400 200 Q 180 200 180 320 Q 180 440 400 440 Q 620 440 620 320 Q 620 200 400 200 Z M 400 270 Q 540 270 540 320 Q 540 370 400 370 Q 260 370 260 320 Q 260 270 400 270 Z M 400 510 Q 100 510 100 320 Q 100 150 300 120 L 300 200 Q 180 220 180 320 Q 180 440 400 440 Q 620 440 620 320 Q 620 130 400 130 Q 300 130 250 180 L 180 130 Q 250 50 400 50 Q 700 580 700 320 Q 700 510 400 510 Z',
        ' ' => '', // Space character has no path
    ];

    /**
     * Convert text string to SVG path elements
     *
     * @param string $text Text to convert
     * @param float $x X position (percentage or absolute)
     * @param float $y Y position (percentage or absolute)
     * @param int $fontSize Font size in pixels
     * @param string $color Fill color
     * @param int $fontWeight Font weight (400 or 600)
     * @param int $viewBoxSize SVG viewBox size
     * @return string SVG path elements
     */
    public static function convertTextToPath(
        string $text,
        $x,
        $y,
        int $fontSize,
        string $color,
        int $fontWeight = 400,
        int $viewBoxSize = 64
    ): string {
        $text = strtoupper($text); // Convert to uppercase
        $chars = str_split($text);
        $pathElements = [];

        // Calculate scaling factor from font em-square (1000) to desired fontSize
        // The font size in SVG is based on the em-square size
        $scale = $fontSize / 1000;

        // Calculate starting X position
        // For centered text, we need to calculate total width
        $charWidth = 800; // Approximate width per character in font units
        $spacing = 100;   // Space between characters in font units
        $totalWidth = (count($chars) * $charWidth + (count($chars) - 1) * $spacing) * $scale;

        // If x is percentage (50%), calculate pixel position
        if ($x === '50%') {
            $startX = ($viewBoxSize - $totalWidth) / 2;
        } else {
            $startX = floatval($x);
        }

        // If y is percentage (50%), center vertically
        // SVG text baseline is different from center, adjust accordingly
        if ($y === '50%') {
            $startY = $viewBoxSize / 2 + ($fontSize * 0.35); // Approximate vertical centering
        } else {
            $startY = floatval($y);
        }

        $currentX = $startX;

        foreach ($chars as $char) {
            if (!isset(self::$glyphPaths[$char])) {
                // Unknown character, skip
                $currentX += ($charWidth + $spacing) * $scale;
                continue;
            }

            $pathData = self::$glyphPaths[$char];

            if ($pathData === '') {
                // Space character
                $currentX += ($charWidth + $spacing) * $scale;
                continue;
            }

            // Create transform for this character: scale and translate
            // Transform origin is at (0, 0), we need to:
            // 1. Scale the path
            // 2. Translate to position
            // 3. Adjust Y for baseline (font coordinates are typically inverted)

            $transform = sprintf(
                'translate(%f,%f) scale(%f,%f) translate(0,-700)',
                $currentX,
                $startY,
                $scale,
                $scale
            );

            // Apply bold effect if needed (stroke-width increase)
            $strokeWidth = $fontWeight === 600 ? 'stroke-width="15" stroke="#' . $color . '"' : '';

            $pathElements[] = sprintf(
                '<path d="%s" fill="#%s" %s transform="%s"/>',
                htmlspecialchars($pathData, ENT_QUOTES | ENT_XML1),
                htmlspecialchars($color, ENT_QUOTES),
                $strokeWidth,
                $transform
            );

            $currentX += ($charWidth + $spacing) * $scale;
        }

        return implode("\n", $pathElements);
    }
}

// Example usage demonstrating the feature:

/*
// In api/index.php, modify the SVG generation section:

if ( $input->format === 'svg' ) {
    header( 'Content-type: image/svg+xml' );

    $initials = htmlspecialchars(
        $avatar->name( $input->name )
               ->length( $input->length )
               ->keepCase( ! $input->uppercase )
               ->getInitials(),
        ENT_QUOTES | ENT_XML1,
        'UTF-8'
    );

    $background = preg_replace( '/[^a-fA-F0-9#]/', '', trim( $input->background, '#' ) );
    $color      = preg_replace( '/[^a-fA-F0-9#]/', '', trim( $input->color, '#' ) );
    $fontSize   = round( $input->size * $input->fontSize );

    echo '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="' . $input->size . 'px" height="' . $input->size . 'px" viewBox="0 0 ' . $input->size . ' ' . $input->size . '" version="1.1">';
    echo '<' . ( $input->rounded ? 'circle' : 'rect' ) . ' fill="#' . $background . '" cx="' . ( $input->size / 2 ) . '" width="' . $input->size . '" height="' . $input->size . '" cy="' . ( $input->size / 2 ) . '" r="' . ( $input->size / 2 ) . '"/>';

    // Check if text-to-path conversion is requested
    if ( $input->textToPath ) {
        // Convert text to paths
        echo TextToPathConverter::convertTextToPath(
            $initials,
            '50%',
            '50%',
            $fontSize,
            $color,
            $input->bold ? 600 : 400,
            $input->size
        );
    } else {
        // Use standard text element
        echo '<text x="50%" y="50%" style="color: #' . $color . '; line-height: 1;font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', \'Roboto\', \'Oxygen\', \'Ubuntu\', \'Fira Sans\', \'Droid Sans\', \'Helvetica Neue\', sans-serif;" alignment-baseline="middle" text-anchor="middle" font-size="' . $fontSize . '" font-weight="' . ( $input->bold ? 600 : 400 ) . '" dy=".1em" dominant-baseline="middle" fill="#' . $color . '">' . $initials . '</text>';
    }

    echo '</svg>';
    return;
}
*/

// Test the converter:
if (php_sapi_name() === 'cli') {
    echo "Testing TextToPathConverter...\n\n";

    $result = TextToPathConverter::convertTextToPath(
        'AB',
        '50%',
        '50%',
        28,
        '222',
        400,
        64
    );

    echo "Sample path output:\n";
    echo $result . "\n\n";

    echo "Full SVG example:\n";
    echo '<svg xmlns="http://www.w3.org/2000/svg" width="64px" height="64px" viewBox="0 0 64 64">' . "\n";
    echo '  <rect fill="#ddd" width="64" height="64"/>' . "\n";
    echo $result . "\n";
    echo '</svg>' . "\n";
}
