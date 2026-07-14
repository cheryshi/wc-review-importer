<?php
/**
 * Product matching contract.
 *
 * @package WCRI\Product
 */

declare(strict_types=1);

namespace WCRI\Product;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Defines a product matching strategy.
 */
interface ProductMatcherInterface
{
    /**
     * Matches a product reference to a WooCommerce product.
     *
     * @param string $product_reference Product reference from imported data.
     * @return ProductMatchResult
     */
    public function match(string $product_reference): ProductMatchResult;
}
