<?php

require 'vendor/autoload.php';

class scrap extends db{

    public $liveURLArray, $tyreSeason, $URLArray = [];
    public $httpClient;

    function __construct(){
        parent::__construct();
        $this->httpClient = new \GuzzleHttp\Client(array( 'curl' => array( CURLOPT_SSL_VERIFYPEER => false, ), ));
        $this->tyreSeason = [
            'S' => 'Summer',
            'W' => 'Winter',
            'I' => 'Ice'
        ];
        $this->URLArray = [
            1 => 'https://www.oponeo.pl/',
            2 => 'https://www.justtyres.co.uk/',
            3 => 'https://www.b-quik.com/en/'
        ];

        $this->liveURLArray = [
            'justtyres' => [
                'url' => 'https://www.justtyres.co.uk/buy-tyres-online',
                'scrapurl' => 2,
                'brandTiles' => '//div[@class="splide__list"]/div[@class="splide__slide"]/div/a',
                'productLinkClass' => '//div[contains(@class,"product-grid-item")]//a/attribute::href',
                'productNameClass' => '//h1[@class="text-primary text-uppercase"]',
                'productCodeClass' => '//div[@class="text-septenary fs-3 fw-bold"]',
                'productPriceClass' => '//div[@class="product-price"]/div[@class="text-primary fs-1 fw-bold"]',

            ],
            'b-quik' => [
                'url' => 'https://www.b-quik.com/en/product/tyre',
                'scrapurl' => 3,
                'brandTiles' => '//div[@class="splide__list"]/div[@class="splide__slide"]/div/a',
                'productLinkClass' => '//div[@class="col-xl-4 col-md-6 text-center img-bottom"]/a/attribute::href',
                'productNameClass' => '//div[@class="col-xl-6"]/h4[2]',
                'productCodeClass' => '//div[@class="table-responsive mb-2"]/table/tbody/tr[1]/td[2]',
                'productPriceClass' => '//div[@class="price-24 pro-img-branch-de2"]',
                
            ]
        ];
    }

    function guzzleXpathHTMLReturn($PageURL, $pageHTMLQuery = null, $returnAll =  false) {
        $mainSiteResponse = $this->httpClient->get($PageURL);
        $mainSiteBody = (string) $mainSiteResponse->getBody()->getContents();
        
        // HTML is often wonky, this suppresses a lot of warnings for xml handling
        libxml_use_internal_errors(true);

        $mainSiteDoc = new DOMDocument();
        $mainSiteDoc->loadHTML($mainSiteBody);
        $mainSiteXPath = new DOMXPath($mainSiteDoc);
        if($returnAll){
            return $mainSiteXPath->query($pageHTMLQuery);
        }else{
            return $mainSiteXPath;
        }        
    }

    function tyreDetails($TyreCode) {
        /*This function will split Tyre code and return array in
            215/55R17 98W => [
                215 = Tyre Width,
                55 = Aspect Ratio,
                R = Tyre construction,
                17 = Tyre diameter,
                98 = Tyre maximum weight,
                W = Tyre speed rating
            ]
                For More details check:
                https://www.halfords.com/tyres/how-to-guides/tyre-markings-explained.html
        */
        
        preg_match_all('/([0-9]+|[a-zA-Z]+)/', $TyreCode, $tyreCodeSplitArray);
        return $tyreCodeSplitArray[0];
    }
    
    public function index() {
        foreach ($this->liveURLArray as $key => $liveURLValue) {
            if($liveURLValue['scrapurl'] === 2){
                $mainSiteXPath = $this->guzzleXpathHTMLReturn(
                    $liveURLValue['url'],
                    $liveURLValue['brandTiles'],
                );

                $tyreBrandsArray = $mainSiteXPath->query($liveURLValue['brandTiles']);
                // Get each Tyre link as of now we only restrict to fetch only 3 Tyre Brands
                for ($i=0; $i < 10 ; $i++) { 
                    $tyreBrandURL = $tyreBrandsArray[$i]->getAttribute("href");
                    $tyreBrandName = $mainSiteXPath->query('img[@class="img-fluid"]', $tyreBrandsArray[$i])->item(0)->getAttribute("alt");
                    echo $tyreBrandName . ' => ' . $tyreBrandURL . '<br>';
                    
                    // Now Let's fetch Tyre tiles for each brand
                    $tyreBrandProductDetailArray = $this->guzzleXpathHTMLReturn(
                        $tyreBrandURL,
                        $liveURLValue['productLinkClass'],
                        true
                    );
                    $this->scrapProductDataURL($tyreBrandProductDetailArray, $liveURLValue);
                }
            }
            if($liveURLValue['scrapurl'] === 3){
                $mainSiteXPath1 = $this->guzzleXpathHTMLReturn(
                    $liveURLValue['url']
                );
                
                $tyreBrandProductDetailArray = $mainSiteXPath1->query($liveURLValue['productLinkClass']);
                $this->scrapProductDataURL($tyreBrandProductDetailArray, $liveURLValue);
                /*
                $mainSiteXPath = $mainSiteXPath1->query('//div[@class="product_div"]/div');
                $prd = 0;
                foreach ($mainSiteXPath as $key => $value) {
                    //var_dump($value);
                    $tyreBrandNames = $tyreBrandProductDetailArray[$prd]->value;
                    echo $value->className . ' => ' . $value->nodeValue . ' <=>' . $tyreBrandNames . '<br>';
                    $prd++;
                }*/
            }
        }
    }

    function scrapProductDataURL($tyreBrandProductDetailArray, $liveURLValue) {
        $numLoop = ($tyreBrandProductDetailArray->length > 20) ? 20 : $tyreBrandProductDetailArray->length; 
        // Tyre Links
        for ($t=0; $t < $numLoop; $t++) {
            $tyreBrandProductDetail = $tyreBrandProductDetailArray[$t];
            
            $tyreDetailPage = $this->guzzleXpathHTMLReturn(
                $tyreBrandProductDetail->nodeValue
            );
            $scrapURL = $liveURLValue['scrapurl'];
            $ProductName = $tyreDetailPage->query($liveURLValue['productNameClass'])->item(0)->nodeValue;
            $ProductCode = $tyreDetailPage->query($liveURLValue['productCodeClass'])->item(0)->nodeValue;
            $ProductPrice = $tyreDetailPage->query($liveURLValue['productPriceClass'])->item(0)->nodeValue;
            $ProductInfoArray = $tyreDetailPage->query('//*[@id="keyInfo"]/div//div[contains(@class,"col-10")]');
            foreach ($ProductInfoArray as $key => $ProductInfo) {
                echo 'Brij => ' . $ProductInfo->textContent;
            }
            $productCodeArray = $this->tyreDetails($ProductCode);
            if(sizeof($productCodeArray) == 4){
                array_push($productCodeArray, 0, 0);
            }
            $productSeason = '';
            $productPattern = '';
            $productExtra = '';
            $this->insertData($scrapURL, $ProductName, $ProductPrice, $productCodeArray[0], $productCodeArray[1], $productCodeArray[2], $productCodeArray[3], $productCodeArray[4], $productCodeArray[5], $productSeason, $productPattern, $productExtra);
            //echo((string)$ProductName);
            echo '========>';
            print_r([$ProductInfoArray, $ProductName, $this->tyreDetails($ProductCode), $ProductPrice]);
            sleep(10);
        }
    }

    function insertData($scrapURL, $productName, $productPrice, $productWidth, $productRatio, $productConstruction, $productDiameter, $productWeight, $productSpeed, $productSeason, $productPattern, $productExtra = '') {

        $insert_product = "insert into product(scrapurl,productname,productprice,productwidth,productratio,productconstruction,productdiameter,productweight,productspeed,productseason,productpattern,productextra) values (
            '" . mysqli_real_escape_string($this->conn, $scrapURL) ."',
            '" . mysqli_real_escape_string($this->conn, $productName) ."',
            '" . mysqli_real_escape_string($this->conn, $productPrice) ."',
            '" . mysqli_real_escape_string($this->conn, $productWidth) ."',
            '" . mysqli_real_escape_string($this->conn, $productRatio) ."',
            '" . mysqli_real_escape_string($this->conn, $productConstruction) ."',
            '" . mysqli_real_escape_string($this->conn, $productDiameter) ."',
            '" . mysqli_real_escape_string($this->conn, $productWeight) ."',
            '" . mysqli_real_escape_string($this->conn, $productSpeed) ."',
            '" . mysqli_real_escape_string($this->conn, $productSeason) ."',
            '" . mysqli_real_escape_string($this->conn, $productPattern) ."',
            '" . mysqli_real_escape_string($this->conn, $productExtra) ."'
        )";
        if (mysqli_query($this->conn, $insert_product)) {
            //$last_inserted_Product = mysqli_insert_id($this->conn);
            return true;
        }
        else{
            return 0;
        }
    }

    function extractcsv() {
        ob_start();
        header( 'Content-Type: application/csv' );
        header( 'Content-Disposition: attachment; filename=tyre_export.csv' );

        // clean output buffer
        ob_end_clean();

        $productCSVOutput = fopen('php://output', 'w');
        fputcsv($productCSVOutput, ['scrapURL', 'productName', 'productPrice', 'productWidth', 'productRatio', 'productConstruction', 'productDiameter', 'productWeight', 'productSpeed', 'productSeason', 'productPattern', 'productExtra']);

        $get_all_Product = 'SELECT scrapurl,productname,productprice,productwidth,productratio,productconstruction,productdiameter,productweight,productspeed,productseason,productpattern,productextra FROM product';
        $result = mysqli_query($this->conn, $get_all_Product);

		if ($result->num_rows > 0) {
			while($row = mysqli_fetch_assoc($result)) {
                $row['scrapurl'] = $this->URLArray[$row['scrapurl']];
			    fputcsv($productCSVOutput, $row);
			}
		}
        fclose( $productCSVOutput );

        // flush buffer
        ob_flush();
        
        // use exit to get rid of unexpected output afterward
        exit();

    }
}
?>