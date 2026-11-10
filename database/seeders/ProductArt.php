<?php

namespace Database\Seeders;

/**
 * Demo product ছবি: প্রতিটা product-এর জন্য নিজে আঁকা সহজ SVG illustration।
 * (শুধু seeder-এর demo data; আসল ছবি admin/owner panel থেকে upload করা যায়)
 */
class ProductArt
{
    /** @param string $kind tshirt|panjabi|headphones|watch|rice|powerbank|box */
    public static function svg(string $kind, string $color, string $label): string
    {
        $dark = self::shade($color, -0.25);
        $light = self::shade($color, 0.35);
        $label = htmlspecialchars($label, ENT_QUOTES);

        $shape = match ($kind) {
            'tshirt' => <<<S
<path d="M135 70 L172 52 Q200 82 228 52 L265 70 L312 118 L280 152 L258 134 L258 252 L142 252 L142 134 L120 152 L88 118 Z" fill="{$color}" stroke="{$dark}" stroke-width="3" stroke-linejoin="round"/>
<path d="M172 52 Q200 82 228 52 Q200 104 172 52 Z" fill="{$dark}"/>
<path d="M142 236 L258 236" stroke="{$dark}" stroke-width="3" opacity=".35"/>
S,
            'panjabi' => <<<S
<path d="M150 56 L176 46 Q200 70 224 46 L250 56 L296 112 L272 144 L252 126 L258 268 L142 268 L148 126 L128 144 L104 112 Z" fill="{$color}" stroke="{$dark}" stroke-width="3" stroke-linejoin="round"/>
<path d="M176 46 Q200 70 224 46 L214 150 L186 150 Z" fill="{$light}" stroke="{$dark}" stroke-width="2"/>
<line x1="200" y1="62" x2="200" y2="150" stroke="{$dark}" stroke-width="2"/>
<circle cx="200" cy="82" r="4" fill="{$dark}"/><circle cx="200" cy="104" r="4" fill="{$dark}"/><circle cx="200" cy="126" r="4" fill="{$dark}"/>
<path d="M148 252 L252 252" stroke="{$light}" stroke-width="6" stroke-dasharray="10 6"/>
S,
            'headphones' => <<<S
<path d="M108 176 A92 92 0 0 1 292 176" fill="none" stroke="{$dark}" stroke-width="16" stroke-linecap="round"/>
<rect x="84" y="160" width="48" height="86" rx="20" fill="{$color}" stroke="{$dark}" stroke-width="3"/>
<rect x="268" y="160" width="48" height="86" rx="20" fill="{$color}" stroke="{$dark}" stroke-width="3"/>
<rect x="96" y="176" width="24" height="54" rx="12" fill="{$light}"/>
<rect x="280" y="176" width="24" height="54" rx="12" fill="{$light}"/>
S,
            'watch' => <<<S
<rect x="172" y="30" width="56" height="240" rx="14" fill="{$dark}"/>
<rect x="138" y="92" width="124" height="124" rx="32" fill="#111827" stroke="{$color}" stroke-width="6"/>
<circle cx="200" cy="154" r="46" fill="{$light}"/>
<line x1="200" y1="154" x2="200" y2="126" stroke="#111827" stroke-width="5" stroke-linecap="round"/>
<line x1="200" y1="154" x2="222" y2="164" stroke="#111827" stroke-width="4" stroke-linecap="round"/>
<circle cx="200" cy="154" r="4" fill="#111827"/>
<rect x="262" y="140" width="8" height="26" rx="3" fill="{$color}"/>
S,
            'rice' => <<<S
<path d="M128 92 Q200 72 272 92 L290 252 Q200 276 110 252 Z" fill="{$color}" stroke="{$dark}" stroke-width="3" stroke-linejoin="round"/>
<path d="M128 92 Q200 112 272 92" fill="none" stroke="{$dark}" stroke-width="3"/>
<path d="M150 74 Q200 52 250 74 L240 92 Q200 82 160 92 Z" fill="{$dark}"/>
<rect x="148" y="140" width="104" height="70" rx="8" fill="#ffffff"/>
<text x="200" y="170" font-family="Arial, sans-serif" font-size="20" font-weight="700" fill="{$dark}" text-anchor="middle">RICE</text>
<text x="200" y="196" font-family="Arial, sans-serif" font-size="16" fill="{$dark}" text-anchor="middle">5 kg</text>
S,
            'powerbank' => <<<S
<rect x="134" y="44" width="132" height="212" rx="22" fill="{$color}" stroke="{$dark}" stroke-width="3"/>
<rect x="152" y="66" width="96" height="52" rx="8" fill="#111827"/>
<text x="200" y="102" font-family="Arial, sans-serif" font-size="26" font-weight="700" fill="#34d399" text-anchor="middle">87%</text>
<circle cx="164" cy="150" r="5" fill="{$light}"/><circle cx="184" cy="150" r="5" fill="{$light}"/><circle cx="204" cy="150" r="5" fill="{$light}"/><circle cx="224" cy="150" r="5" fill="#9ca3af"/>
<rect x="176" y="236" width="48" height="10" rx="3" fill="{$dark}"/>
<path d="M205 170 L188 204 L201 204 L195 232 L216 194 L202 194 Z" fill="{$light}"/>
S,
            default => <<<S
<path d="M200 70 L290 110 L290 214 L200 254 L110 214 L110 110 Z" fill="{$color}" stroke="{$dark}" stroke-width="3" stroke-linejoin="round"/>
<path d="M200 70 L200 254 M110 110 L200 150 L290 110" fill="none" stroke="{$dark}" stroke-width="3"/>
S,
        };

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="400" height="300" viewBox="0 0 400 300">
  <rect width="400" height="300" fill="#f1f5f9"/>
  {$shape}
  <text x="200" y="290" font-family="Arial, sans-serif" font-size="14" fill="#64748b" text-anchor="middle">{$label}</text>
</svg>
SVG;
    }

    /** রং হালকা (+) বা গাঢ় (-) করে */
    private static function shade(string $hex, float $amount): string
    {
        $hex = ltrim($hex, '#');
        $out = '#';
        foreach ([0, 2, 4] as $i) {
            $c = hexdec(substr($hex, $i, 2));
            $c = $amount >= 0 ? $c + (255 - $c) * $amount : $c * (1 + $amount);
            $out .= str_pad(dechex((int) round(max(0, min(255, $c)))), 2, '0', STR_PAD_LEFT);
        }

        return $out;
    }
}