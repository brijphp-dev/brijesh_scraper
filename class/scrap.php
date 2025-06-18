<?php

require 'vendor/autoload.php';
class scrap extends db{

    public $liveURLArray = [];
    //public $httpClient;
    function __construct(){
        
        $this->liveURLArray = [
            /*'oponeo' => [
                'url' => 'https://www.oponeo.pl/',
                'subMenu' => [
                    'opony'
                ],
                'gridClass' => 'div.promotedProduct'
            ],*/
            'justtyres' => [
                'url' => 'https://www.justtyres.co.uk/',
                'subMenu' => [
                    'brands/michelin-tyres'
                ],
                'productLink' => 'https://www.justtyres.co.uk/products/',
                'productLinkClass' => '//div[@class="related-products-grid"]//div[@class="product-grid-item"]//a[@class="stretched-link"]',
                'nameClass' => '//div[@class="related-products-grid"]//div[@class="product-grid-item"]//h4[@class="text-uppercase"]',
                'priceClass' => '//div[@class="related-products-grid"]//div[@class="product-grid-item"]//div[@class="product-price"]',

            ],
            /*'b-quik' => [
                'url' => 'https://www.b-quik.com/en/',
                'subMenu' => [
                    'product/tyre'
                ],
                'gridClass' => 'div.container-fluid product'
            ]*/
        ];
    }
	
	public function index(){
        foreach ($this->liveURLArray as $liveURLKey => $liveURLValue) {
            $siteurl = $liveURLValue['url'];

            foreach ($liveURLValue['subMenu'] as $subMenuKey => $subMenuValue) {
                $liveurl = $siteurl . $subMenuValue;
                $httpClient = new \GuzzleHttp\Client(array( 'curl' => array( CURLOPT_SSL_VERIFYPEER => false, ), ));

                $response = $httpClient->get($liveurl);
                $htmlString = (string) $response->getBody();
                libxml_use_internal_errors(true);

                $doc = new DOMDocument();
                $doc->loadHTML($htmlString);

                $xpath = new DOMXPath($doc);

                //$productLinks[] = $xpath->evaluate('h4[@class="text-uppercase"]'); // //div[@class="col-xxl-3"]
                $productName = $xpath->evaluate('div[@class="product-grid-item"]//a[@class="stretched-link"]');
                $prices = $xpath->evaluate($liveURLValue['priceClass']);
                $productNameArray = [];
                foreach ($productName as $key => $product) {
                    $productNameArray[] = $product->textContent;
                }
                /*$priceArray = [];
                foreach ($prices as $key => $price) {
                    $priceArray[] = $price->nodeValue;
                }*/
                print_r($productNameArray);
            }
        }
	}
	
	public function index1(){
		$httpClient = new \GuzzleHttp\Client(array( 'curl' => array( CURLOPT_SSL_VERIFYPEER => false, ), ));

        $response = $httpClient->get('https://books.toscrape.com/');

        $htmlString = (string) $response->getBody();

        // HTML is often wonky, this suppresses a lot of warnings
        libxml_use_internal_errors(true);

        $doc = new DOMDocument();
        $doc->loadHTML($htmlString);

        $xpath = new DOMXPath($doc);

        $names = $xpath->evaluate('//ol//h3//a');
        $prices = $xpath->evaluate('//ol//div[@class="product_price"]//p[@class="price_color"]');
        $priceArray = [];
        foreach ($prices as $key => $price) {
        $priceArray[] = $price->textContent;
        }
        foreach ($names as $aKey => $name) {
            echo 'Book Name: ' . $name->textContent . 'With Price: @' . $prices[$aKey]->textContent .'<br>';
        }
	}

}
?>