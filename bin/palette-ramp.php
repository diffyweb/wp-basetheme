<?php
/**
 * Print or check the tint and shade ramp for a theme.json palette color.
 *
 *   php bin/palette-ramp.php SLUG "Display Name" "#rrggbb"   print the 19 palette entries
 *   php bin/palette-ramp.php --check theme.json              recompute every ramp in the file
 *
 * Each ramp runs {slug}-90-lighter ... {slug} ... {slug}-90-darker in 10% steps.
 * Per channel (0-255), with $p = $n * 0.01:
 *   darker:  floor($c * (1 - $p) + 0.5)
 *   lighter: floor($c + (255 - $c) * $p + 0.5)
 *
 * Keep $p as the float $n * 0.01. Dividing ($n / 100) rounds exact .5 ties
 * differently and changes some existing entries (primary-70-darker was the
 * one that exposed it), so the ramps already in theme.json would stop matching.
 *
 * Plain PHP, no WordPress load. Needs PHP 7.4 or newer (the theme's floor);
 * tested on PHP 8.5.12.
 */

const STEPS = array( 10, 20, 30, 40, 50, 60, 70, 80, 90 );
const HEX   = '/^#[0-9a-fA-F]{6}\z/';
const SLUG  = '/^[a-z][a-z0-9]*(-[a-z0-9]+)*\z/';
const RAMP  = '/^(.+)-(\d+)-(lighter|darker)$/';

function channel( int $c, int $n, string $kind ): int {
	$p     = $n * 0.01;
	$value = 'darker' === $kind ? $c * ( 1 - $p ) : $c + ( 255 - $c ) * $p;
	return (int) floor( $value + 0.5 );
}

function shade( string $base_hex, int $n, string $kind ): string {
	$out = '#';
	foreach ( array( 1, 3, 5 ) as $i ) {
		$out .= sprintf( '%02x', channel( hexdec( substr( $base_hex, $i, 2 ) ), $n, $kind ) );
	}
	return $out;
}

/** @return array<int, array{0:string,1:string,2:string}> slug, name, color */
function ramp( string $slug, string $name, string $base_hex ): array {
	$entries = array();
	foreach ( array_reverse( STEPS ) as $n ) {
		$entries[] = array( "$slug-$n-lighter", "$name $n% Lighter", shade( $base_hex, $n, 'lighter' ) );
	}
	$entries[] = array( $slug, $name, strtolower( $base_hex ) );
	foreach ( STEPS as $n ) {
		$entries[] = array( "$slug-$n-darker", "$name $n% Darker", shade( $base_hex, $n, 'darker' ) );
	}
	return $entries;
}

function read_palette( string $path ): array {
	$raw = @file_get_contents( $path );
	if ( false === $raw ) {
		throw new RuntimeException( 'OSError: cannot open file' );
	}
	$data = json_decode( $raw, true );
	if ( JSON_ERROR_NONE !== json_last_error() ) {
		throw new RuntimeException( 'JSONDecodeError: ' . json_last_error_msg() );
	}
	if ( ! isset( $data['settings']['color']['palette'] ) || ! is_array( $data['settings']['color']['palette'] ) ) {
		throw new RuntimeException( 'KeyError: settings.color.palette is missing or not a list' );
	}
	$palette = $data['settings']['color']['palette'];
	foreach ( $palette as $entry ) {
		if ( ! is_array( $entry ) ) {
			throw new RuntimeException( 'TypeError: palette entry is not an object' );
		}
		foreach ( array( 'slug', 'name', 'color' ) as $key ) {
			if ( ! array_key_exists( $key, $entry ) ) {
				throw new RuntimeException( "KeyError: '$key'" );
			}
			if ( ! is_string( $entry[ $key ] ) ) {
				throw new RuntimeException( "TypeError: palette entry has no text '$key'" );
			}
		}
	}
	return array_values( $palette );
}

function check( string $path ): int {
	try {
		$palette = read_palette( $path );
	} catch ( RuntimeException $e ) {
		echo "cannot read palette from $path: " . $e->getMessage() . "\n";
		return 1;
	}
	$by_slug = array();
	$slugs   = array();
	foreach ( $palette as $entry ) {
		$by_slug[ $entry['slug'] ] = $entry; // Later duplicates win, like a dict.
		$slugs[]                   = $entry['slug'];
	}
	$bases = array();
	foreach ( array_keys( $by_slug ) as $s ) {
		if ( preg_match( RAMP, (string) $s, $m ) ) {
			$bases[ $m[1] ] = true;
		}
	}
	$bases = array_map( 'strval', array_keys( $bases ) );
	sort( $bases, SORT_STRING );

	$problems = array();
	$checked  = 0;
	foreach ( $slugs as $slug ) {
		$slug = (string) $slug;
		if ( 'white' !== $slug && 'black' !== $slug && ! preg_match( RAMP, $slug ) && ! in_array( $slug, $bases, true ) ) {
			$problems[] = "$slug: color has no ramp";
		}
	}
	foreach ( array( 'white', 'black', 'gray' ) as $needed ) {
		if ( ! isset( $by_slug[ $needed ] ) ) {
			$problems[] = "$needed: missing, it stays on every site";
		}
	}
	if ( ! in_array( 'gray', $bases, true ) ) {
		$problems[] = 'gray: missing ramp, it stays on every site';
	}
	foreach ( $bases as $base ) {
		if ( ! isset( $by_slug[ $base ] ) ) {
			$problems[] = "$base: ramp has no base color entry";
			continue;
		}
		$color = $by_slug[ $base ]['color'];
		if ( ! preg_match( HEX, $color ) ) {
			$problems[] = "$base: base color is not #rrggbb";
			continue;
		}
		$expected = ramp( $base, $by_slug[ $base ]['name'], $color );
		$order    = array_column( $expected, 0 );
		$members  = array();
		foreach ( $slugs as $k => $s ) {
			$s = (string) $s;
			if ( in_array( $s, $order, true ) || ( 0 === strpos( $s, $base . '-' ) && preg_match( RAMP, $s, $m ) && $m[1] === $base ) ) {
				$members[] = $k;
			}
		}
		$ours = array();
		foreach ( $members as $k ) {
			$ours[] = (string) $slugs[ $k ];
		}
		if ( $ours !== $order ) {
			$problems[] = "$base: ramp is incomplete, off-grid or out of order";
		}
		if ( $members && end( $members ) - $members[0] + 1 !== count( $members ) ) {
			$problems[] = "$base: ramp entries are not contiguous in the palette";
		}
		foreach ( $expected as $row ) {
			list( $slug, $name, $shade_hex ) = $row;
			if ( $slug === $base || ! isset( $by_slug[ $slug ] ) ) {
				continue;
			}
			++$checked;
			$entry = $by_slug[ $slug ];
			if ( strtolower( $entry['color'] ) !== $shade_hex ) {
				$problems[] = "$slug: file {$entry['color']}, formula $shade_hex";
			}
			if ( $entry['name'] !== $name ) {
				$problems[] = "$slug: name is " . json_encode( $entry['name'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . ', expected ' . json_encode( $name, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
			}
		}
	}
	echo 'ramps: ' . implode( ', ', $bases ) . "; $checked entries checked, " . count( $problems ) . " problems\n";
	foreach ( $problems as $line ) {
		echo '  ' . $line . "\n";
	}
	return $problems ? 1 : 0;
}

function main( array $argv ): int {
	if ( 3 === count( $argv ) && '--check' === $argv[1] ) {
		return check( $argv[2] );
	}
	if ( 4 === count( $argv ) && preg_match( HEX, $argv[3] ) && preg_match( SLUG, $argv[1] ) ) {
		$flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
		foreach ( ramp( $argv[1], $argv[2], $argv[3] ) as $row ) {
			list( $slug, $name, $color ) = $row;
			echo "\t\t\t\t{\n\t\t\t\t\t\"color\": " . json_encode( $color, $flags ) . ",\n\t\t\t\t\t\"name\": " . json_encode( $name, $flags ) . ",\n\t\t\t\t\t\"slug\": " . json_encode( $slug, $flags ) . "\n\t\t\t\t},\n";
		}
		return 0;
	}
	echo "Usage:\n  php bin/palette-ramp.php SLUG \"Display Name\" \"#rrggbb\"\n  php bin/palette-ramp.php --check theme.json\n";
	return 2;
}

exit( main( $argv ) );
