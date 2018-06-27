<?php
class Reviewscouk_Reviews_Block_Reviews_Footer extends Reviewscouk_Reviews_Block_Reviews_Abstract
{

    /*
     * Include Rich Snippet - Auto Detect product
     */
    public function includeRichSnippet()
    {
        $merchant_enabled = $this->helper->config('rich_snippet/rich_snippet_enabled');
        $product_enabled  = $this->helper->config('rich_snippet/product_rich_snippet_enabled');

        $current_product = Mage::registry('current_product');

        if ($current_product && $product_enabled) {
            $sku = $this->helper->getProductSkus($current_product);

            $product = [
                'availability'  => $this->availability($current_product->getData('is_in_stock')),
                'price'         => $current_product->getData('price'),
                'priceCurrency' => Mage::app()->getStore()->getCurrentCurrencyCode(),
                'product'       => $current_product->getData(),
            ];

            return $this->getRichSnippet($sku, $product);
        } elseif ($merchant_enabled) {
            return $this->getRichSnippet();
        }
        return '';
    }

    /*
     * Get Rich Snippet Code for sku
     */
    public function getRichSnippet($sku = null, $product = null)
    {
        if (isset($sku) && is_array($sku)) {
            $sku = implode(';', $sku);
        }

        $output = '<script src="' . $this->helper->getReviewsUrl('widget') . 'rich-snippet/dist.js' . '"></script>';
        $output .= '<script>

        richSnippet({

            store: "' . $this->helper->getStoreName() . '",
            sku:"' . $sku . '",
            data:{
                type:"Offer",
                availability: "' . (isset($product['availability']) ? $product['availability'] : null) . '",
                price: "' . (isset($product['price']) ? $product['price'] : null) . '",
                priceCurrency: "' . (isset($product['priceCurrency']) ? $product['priceCurrency'] : null) . '"
            }

        })</script>';
        return $output;
    }

    /**
     * Get Availability for Rich Shippets
     * @param $availability -- boolean
     *
     * @return string
     */
    private function availability($availability)
    {
        if ($availability !== '1') {
            return 'http://schema.org/OutOfStock';
        }
        return 'http://schema.org/InStock';
    }

    /*
     * Include Rating Snippet Initialisation Code
     */
    public function includeRatingSnippet()
    {
        $enabled = $this->helper->config('widget/rating_snippet_enabled');

        if ($enabled) {
            $output = '<script src="' . $this->helper->getReviewsUrl('widget') . 'product/dist.js' . '"></script>';
            $output .= '<script src="' . $this->helper->getReviewsUrl('widget') . 'rating-snippet/dist.js' . '"></script>';
            $output .= '<link rel="stylesheet" href="' . $this->helper->getReviewsUrl('widget') . 'rating-snippet/dist.css" />';
            $output .= '<script>ratingSnippet("ruk_rating_snippet", { store: "' . $this->helper->getStoreName() . '", color:"' . $this->helper->getWidgetColor() . '", css: "' . $this->helper->getProductWidgetCss() . '", linebreak: false, text: "Reviews" })</script>';
            return $output;
        }
    }

}
