<?php

require_once 'spider_lib_ag/XPathHelper.php';
require_once 'spider_lib_ag/spider_parser.php';

function my_mb_trim($text)
{
    return trim($text, "\n\r\t " . chr(0xA0) . chr(0xc2));
}

class Spider_AmazonCom_Parser extends Spider_Parser
{

    public function __construct()
    {
        parent::__construct('http://www.amazon.com/');
    }

    public function parseProductDetails($xph)
    {
        //TODO: shoud I move this test to getPageType ?		
        if (0 < $xph->xpQuery("//div[@id='avod-main-container']", -1)->length)
            return array('_spider.status' => 'incomplete');
        $xph->assertEquals('product', $this->parsePageType($xph));

        $result = array();
        $result['product.sellers'] = $xph->xpSubQueries("//div[@id='secondaryUsedAndNew']/div[@class='mbcOlpLink']", array(
            'product.price_listed' => "span[@class='price']",
            'product.condition_and_sellers_count' => "a/text()",
            'sellers.href-local' => "a/@href",
        ));
		
        $result['product.sellers-percondition'] = $xph->xpSubQueries("//div[@id='olpDivId']/span[@class='olpCondLink']", array(
            'product.price_listed' => "span[@class='price']",
            'product.condition_and_sellers_count' => "a/text()",
            'sellers.href-local' => "a/@href",
        ));
        $result = $this->array_merge_first_record($result, $xph->xpSubQueries("//div[@id='product-title_feature_div']/div[@class='buying'] | //form[@id='handleBuy']/div[@class='buying']", array(
                    'product.title' => "h1/span[@id='btAsinTitle']",
                    'brand.name' => "span/a",
                    'brand.href-local' => "span/a/@href",
        )));
        if (array() !== $xph->queryValue("//div[@id='price_feature_div']/div[@id='priceBlock'][@class='pa_PriceBlock']", -1))
            $result = $this->array_merge_first_record($result, $xph->xpSubQueries("//div[@id='price_feature_div']/div[@id='priceBlock'][@class='pa_PriceBlock']", array(
                        'product.price_listed' => "span[@class='pa_price']",
            )));
        elseif (array() !== $xph->queryValue(
                        "//form[@id='handleBuy']/table/tbody/tr/td/div[@id='priceBlock'][@class='pa_PriceBlock']"
                        . "|//form[@id='handleBuy']/table/tr/td/div[@id='priceBlock'][@class='pa_PriceBlock']"
                        , -1)
        )
            $result = $this->array_merge_first_record($result, $xph->xpSubQueries(
                            "//form[@id='handleBuy']/table/tbody/tr/td/div[@id='priceBlock'][@class='pa_PriceBlock']"
                            . "|//form[@id='handleBuy']/table/tr/td/div[@id='priceBlock'][@class='pa_PriceBlock']", array(
                        'product.price_listed' => "span[@class='pa_price']",
            )));
        elseif (array() !== $xph->queryValue("//div[@class='jumpBar']/div/span", -1))
        {
            $result = $this->array_merge_first_record($result, $xph->xpSubQueries("//div[@class='jumpBar']", array(
                        'product.reviews-score-avg' => "div/span/span[@class='crAvgStars']/span[1]/a",
                        'product.reviews-count' => "div/span/span[@class='crAvgStars']/a",
                        'product.likes-count' => "//span[@class='amazonLikeCount']",
                        'product.price_listed' => "//span[@id='actualPriceValue']",
                        'product.price_retail' => "//span[@id='listPriceValue']",
                        'amazon.product.actualPriceExtraMessaging' => "//span[@id='actualPriceExtraMessaging']",
            )));
        }
        elseif (array() !== $xph->queryValue("//div[@class='jumpBar']/span[@class='tiny']", -1))
        {
            $result = $this->array_merge_first_record($result, $xph->xpSubQueries("//div[@class='jumpBar']", array(
                        'product.reviews-score-avg' => "span[@class='tiny']/span[@class='crAvgStars']/span[1]/a",
                        'product.reviews-count' => "span[@class='tiny']/span[@class='crAvgStars']/a",
                        'product.likes-count' => "//span[@class='amazonLikeCount']",
                        'product.price_listed' => "//span[@id='actualPriceValue']",
                        'product.price_retail' => "//span[@id='listPriceValue']",
                        'amazon.product.actualPriceExtraMessaging' => "//span[@id='actualPriceExtraMessaging']",
            )));
        }
        else
        {
            $this->assert(false, "unexpected document structure");
        }

        $result = $this->array_merge_first_record($result, $xph->xpSubQueries("//form[@id='handleBuy']", array(
                    'amazon.session.id' => "//input[@id='session-id']/@value", //type="hidden" value="190-2140005-5524749" name="session-id"/>
                    'product.sku' => "//input[@id='ASIN']/@value", // type="hidden" value="B00746MXF8" name="ASIN"/>
                    'amazon.merchantexclusive' => "//input[@id='isMerchantExclusive']/@value", // type="hidden" value="0" name="isMerchantExclusive"/>
                    'seller.id' => "//input[@id='merchantID']/@value", // type="hidden" value="A3FO2JHXM8NN56" name="merchantID"/>
                    'amazon.node' => "//input[@id='nodeID']/@value", // type="hidden" value="541966" name="nodeID"/>
                    'amazon.listing-id' => "//input[@id='offerListingID']/@value", // type="hidden" value="5NIScdtNGMYO0J%2BXiLgL6SsNPeiaCuZn8M4Mg9I5gi8%2FK0oltswD0pS4Xclt%2FJdp5kUbNASa9DEvoTrqbrMK1SPMcJJm1KiUtK4SklWpkf8uZdsgg5k7y3p6%2FY6O19W6Xza0b6rWKlXeVjgwCg5NpQ%3D%3D" name="offerListingID"/>
                    'amazon.sellingcustomer.id' => "//input[@id='sellingCustomerID']/@value", // type="hidden" value="A3FO2JHXM8NN56" name="sellingCustomerID"/>
                        /* NOTE: ignored form input fields for future reference.
                         * <input[@id="sourceCustomerOrgListID" type="hidden" value="" name="sourceCustomerOrgListID"/>
                          <input[@id="sourceCustomerOrgListItemID" type="hidden" value="" name="sourceCustomerOrgListItemID"/>
                          <input[@id="qid" type="hidden" value="" name="qid"/>
                          <input[@id="sr" type="hidden" value="" name="sr"/>
                          <input[@id="storeID" type="hidden" value="pc" name="storeID"/>
                          <input[@id="tagActionCode" type="hidden" value="" name="tagActionCode"/>
                          <input[@id="viewID" type="hidden" value="glance" name="viewID"/>
                          <input[@id="isAddon" type="hidden" value="0" name="isAddon"/>
                         */
        )));
        $result = $this->array_merge_first_record($result, $xph->xpSubQueries(
                        "//div[@id='availability_feature_div']/div[@class='buying']"
                        . "|//form[@id='handleBuy']/table/tr/td/div[@class='buying'][contains(span/@class,'avail')]"
                        . "|//div[@class='buying'][span/@class='availGreen' or span/@class='availRed' or span/@class='availOrange']"
                        . "|//div[@class='buying'][span/@id='paAvailabilityMessage']"
                        , array(
                    'product.availability' => "span[@class='availGreen' or @class='availRed' or @class='availOrange']|span[@id='paAvailabilityMessage']/div[@class='pa_AvailabilityTitle']/text()",
                    'seller.name' => "b|span[@id='paAvailabilityMessage']/div[@class='pa_AvailabilityContent']/a",
                    'seller.href-local' => "a/@href|b/a/@href|span[@id='paAvailabilityMessage']/div[@class='pa_AvailabilityContent']/a/@href",
                    'product.order._fullfilment_and_availability_and_seller_and_wrap' => ".",
                    'product.order.fullfilment' => "div[@id='availability_feature_div']/div[@class='buying']/span[@id='paAvailabilityMessage']/div[@class='pa_AvailabilityContent']",
                    'product.order._script' => "script",
        )));
        /*
         * disabled because it does not contain actual value selected for variations
          $result['product.variation-selection']=$xph->xpSubQueries("//div[@class='variationSelected']",
          array(
          'product.variation.name' => "b[@class='variationDefault']",
          'product.variation.value' => "b[@class='variationLabel']",//NB: broken because this field is created by javascript.
          'product.variation.id' => "@id",
          ));
         */ $result['product.variation-option'] = $xph->xpSubQueries("//div[@class='clearfix spacediv']/div[@class='swatchOuter']", array(
            '@value' => "div",
            'product.variation.title' => "@title",
            'product.variation.id-css' => "div/@id",
            '@label' => "@key",
            'product.variation.image-css' => "div/div[@class='swatchInnerBorder']/div[@class='swatchInnerImage']/@style",
        ));
        $result['product.variation-selection2'] = $xph->xpSubQueries("//div[@id='feature-bullets_feature_div']/table/tr/td/div[@class='disclaim']/text()", array(
            '@value' => ".",
            '@label' => "following::*[1]",
        ));
        $result['product.features'] = $xph->queryValue("//div[@id='feature-bullets_feature_div']/table/tr/td/div[@class='content']/ul/li", -1);
        /* NB: skipping page sections:
          - Special Offers and Product Promotions
          - Frequently bought together
         */
        //WARNING: some product.details include currently selected variation. They are not fixed values
        $result['product.details'] = $xph->xpSubQueries("//div[@id='product-details-grid_feature_div']/div[@id='prodDetails']/div[@class='wrapper']/div[@class='container']/div[@class='column col1']/div[@class='section techD']/div[@class='content pdClearfix']/div[@class='attrG']/div[@class='pdTab']/table/tbody/tr[td/@class='label']", array(
            '@label' => "td[@class='label']",
            '@value' => "td[@class='value']",
        ));
        $result['amazon.product.additional_information'] = $xph->xpSubQueries("//div[@id='product-details-grid_feature_div']/div[@id='prodDetails']/div[@class='wrapper']/div[@class='column col2']/div[@class='section techD']/div[@class='content pdClearfix']/div[@class='attrG']/div[@class='pdTab']/table/tbody/tr[td/@class='label']", array(
            '@label' => "td[@class='label']",
            '@value' => "td[@class='value'][not(script)]", //avoid loading the large javascript in customer review.
        ));
        $result['amazon.product.sales-outbound'] = $xph->xpSubQueries("//div[@id='conditional-probability_feature_div']/div[@id='cpsims-feature']/div[@id='vtpsims']/ul/li/div[@class='asinDetails']", array(
            'product.title' => "a",
            'product.href-local' => "a/@href",
            'product.image-tiny' => "a/img/@src",
            'brand.name' => "span[@class='vtp-binding-byline']",
            'product.price_listed' => "div[@class='price']",
            'product.sku' => "span[@class='rating-stars']/span[@class='crAvgStars']/span[@class='asinReviewsSummary']/@name",
            'product.reviews-count' => "span[@class='rating-stars']/span[@class='crAvgStars']/a",
            'product.reviews-score-avg' => "span[@class='rating-stars']/span[@class='crAvgStars']/span[@class='asinReviewsSummary']/a",
        ));
        $result['product.description'] = $xph->queryValue("//div[@id='productDescription']/div[@class='content']/div[@class='productDescriptionWrapper']");
        $result['amazon.product.sales-related'] = $xph->xpSubQueries("//div[@id='purchase-similarities_feature_div']/div[@id='purchase-sims-feature']/div[@class='bucket']/div[@class='simsWrapper']/div[@class='shoveler']/div[@class='shoveler-button-wrapper']/div[@class='shoveler-content']/ul/li/div[@class='new-faceout']", array(
//					'raw' => '.',
//					'class' => "div/@class",
//					'id' => "div/@id",
            'product.title' => "a",
            'product.href-local' => "a/@href",
            'product.image-small' => "a/div[@class='product-image']/img/@src",
            'product.price_listed' => "div[@class='pricetext']",
            'product.sku' => "div[@class='rating-price']/span[@class='rating-stars']/span[@class='crAvgStars']/span[@class='asinReviewsSummary']/@name",
            'product.reviews-count' => "div[@class='rating-price']/span[@class='rating-stars']/span[@class='crAvgStars']/a",
            'product.reviews-score-avg' => "div[@class='rating-price']/span[@class='rating-stars']/span[@class='crAvgStars']/span[@class='asinReviewsSummary']/a",
        ));
        $result['amazon.sellers-boxed'] = $xph->xpSubQueries("//table[@class='mbcOffers']/tr/td[@class='mbcOfferRowTD']/table[@class='mbcOfferRow']", array(
            'seller.name' => "tr[@class='mbcMerch']/td[1]",
            'product.price_listed' => "tr/td[@class='mbcPriceCell']/text()",
            'product.price-shipping_and_fullfilment' => "tr/td[@class='mbcPriceCell']/span[@class='plusShippingText']",
            'amazon.sellers-boxed.product.price_hidden' => "tr/td[@class='mbcPriceCell']/span[@id]",
        ));
        $result['_spider.status'] = "complete";
        return $result;
    }

    public function parseSearchResult(XPathHelper $xph)
    {
        if (null !== $xph->xpQuery("//*[@id='noResultsTitle']"))
            return array();

        //INFO: results with ul/@class=tc are not products, they are links to other product categories.
        return $xph->xpSubQueries("//*[@id='atfResults']/div[not(ul/@class='tc')][h3/@class='newaps']", array(
                    //INFO all products search results have h3[@class='newsap']
                    'product.title' => "h3/a",
                    'product.sku' => "@name",
                    'product.href-local' => "h3/a/@href",
                    'brand.name' => "h3/span[@class='med reg']/a",
                    'amazon.product.format' => "h3/span[@class='med reg']/span[@class='bold']",
                    'amazon.product.starring' => "h3/span[@class='med reg']/span[@class='starring']",
                    'brand.name_andall' => "h3/span[@class='med reg']",
                    'brand.href-local' => "h3/span[@class='med reg']/a/@href",
                    'product.sellers-percondition' => array("ul[@class='rsltL']/li[@class='med grey mkp2']", array(
                            'product.price_listed' => "a/span[@class='price bld']/text()",
                            'product.condition' => "a/text()",
                            'sellers-count' => "a/span[@class='grey']/text()",
                            'sellers.href-local' => "a/@href",
                        )),
                    //extract main prices after price per condition as we use those as replacement in translate.
                    'product.availability' => "ul[@class='rsltL']/li/span[@class='grey sml']",
                    'product.price_listed' => "ul[@class='rsltL']/li[@class='newp']/div/a/span[@class='bld lrg red']"
                    . "|ul[@class='rsltL']/li[not(@class)]/span[@class='lrg red bld']",
                    'product.price_retail' => "ul[@class='rsltL']/li[@class='newp']/div/a/del[@class='grey']",
                    //TRICK the 'rsltR dkGrey' value attribute contains many spaces and 1 \n!!
                    'amazon.product.category' => "ul[contains(@class,'rsltR dkGrey')]/li/span[@class='bold orng']",
                    'amazon.product.category-count' => "ul[contains(@class,'rsltR dkGrey')]/li[span/@class='bold orng']/a",
        ));
    }

    public function parseSearchResultPagination(XPathHelper $xph)
    {
        //FIXME: implement parseSearchResultPagination
        return null;
    }

    public function parseOfferSellerList(XPathHelper $xph)
    {
        $result = array();
        $result['offers.condition'] = $xph->queryValue("//table[@class='offerlistings']/tr/td[@class='middle']/div/@id");

        //NB: a major difference with other variations is that the @class=price include shipping cost only for this layout !!!
        $result['offers.sellers'] = $xph->xpSubQueries("//div[@class='resultsset']/table[not(@class)]/tbody/tr[td[6]]", array(
            'product.price_listed' => "td[2]/span[@class='olpSecondaryPrice']",
//NB: could not find this information on data sample.	'amazon.product.actualPriceExtraMessaging' =>
            'offer.price-shipping' => "td[3]/text()[1]",
            //TRICK: we are scraping "supersaver only to confirm that missing shipping price is not due to scraping miss.
            'amazon.offer.supersaver' => "td[3]/span[@class='olpPrimeBlue']",
            'product.condition' => "td[4]/div[@class='condition']",
            //WARNING: the next 3 query are missing the /li/ node because the html structure is incorrect. Amazon might fix it.
//REFACTOR duplicate expressions for following data fields with next layout ( replace 5 with 3)
            'seller.name' => "td[5]/ul[@class='sellerInformation']/a/img/@title"
            . "|td[5]/ul[@class='sellerInformation']/img/@title"
            . "|td[5]/ul[@class='sellerInformation']/li/div[@class='seller']/a",
            'seller.name2' => "td[5]/ul[@class='sellerInformation']/a/img/@alt"
            . "|td[5]/ul[@class='sellerInformation']/img/@alt",
            'seller.href-local' => "td[5]/ul[@class='sellerInformation']/a/@href"
            . "|td[5]/ul[@class='sellerInformation']/li/div[@class='seller']/a/@href",
            'seller.reviews-score-avg' => "td[5]/ul/li/div[@class='rating']/a/b",
            'seller.reviews-count' => "td[5]/ul/li/div[@class='rating']/text()[last()]",
            'product.availability' => "td[5]/ul/li/div[@class='availability']/text()[1]",
            'amazon.seller.shipping-destinations' => "td[5]/ul/li/div[@class='availability']/a[contains(text(),'shipping rates')]",
        ));
        /*
         */
        if ($result['offers.sellers'] !== array())
            return $result;

        $result['offers.sellers'] = $xph->xpSubQueries("//div[@class='resultsset']/table[not(@class)]/tbody/tr[td[4]]", array(
            'product.price_listed' => "td[1]/span[@class='price']",
            'amazon.product.actualPriceExtraMessaging' => "td[1]/span[@class='pricePerUnit']",
            'offer.price-shipping' => "td[1]/div[@class='shipping_block']",
            //TRICK: we are scraping "supersaver only to confirm that missing shipping price is not due to scraping miss.
            //TRICK: therefore [1] does not matter skiping additional supersaver items.
            'amazon.offer.supersaver' => "td[1]/span[@class='supersaver'][1]",
            'product.condition' => "td[2]/div[@class='condition']",
            //WARNING: the next 3 query are missing the /li/ node because the html structure is incorrect. Amazon might fix it.
            'seller.name' => "td[3]/ul[@class='sellerInformation']/a/img/@title"
            . "|td[3]/ul[@class='sellerInformation']/img/@title"
            . "|td[3]/ul[@class='sellerInformation']/li/div[@class='seller']/a",
            'seller.name2' => "td[3]/ul[@class='sellerInformation']/a/img/@alt"
            . "|td[3]/ul[@class='sellerInformation']/img/@alt",
            'seller.href-local' => "td[3]/ul[@class='sellerInformation']/a/@href"
            . "|td[3]/ul[@class='sellerInformation']/li/div[@class='seller']/a/@href",
            'seller.reviews-score-avg' => "td[3]/ul/li/div[@class='rating']/a/b",
            'seller.reviews-count' => "td[3]/ul/li/div[@class='rating']/text()[last()]",
            'product.availability' => "td[3]/ul/li/div[@class='availability']/text()[1]",
            'amazon.seller.shipping-destinations' => "td[3]/ul/li/div[@class='availability']/a[contains(text(),'shipping rates')]",
        ));
        return $result;
    }

    public function parseOfferSellerListPagination(XPathHelper $xph)
    {
        //var_dump($xph->queryValue("//div[@class='pagination']/div[@class='pages']/a[@id='olp_page_next'][@class='nextoff']/@href"));die;
        return $xph->queryValue("//div[@class='pagination']/div[@class='pages']/a[@id='olp_page_next'][@class='nextoff']/@href");
    }
    
	// extract an uri parameter
	// TODO: put this into the helpers file
	public function extractUriParam($paramKey, $url){
		parse_str($url, $arr_q);
		return (!empty($arr_q[$paramKey])) ? $arr_q[$paramKey] : "";
	}
    public function parseOfferMerchantList(XPathHelper $xph)
    {
		$imageUrl = $xph->queryValue('//*[@id="olpProductImage"]/a/img/@src');
		
		$result = $xph->xpSubQueries("//div[@id='olpOfferList']/div[@id='olpTabContent']/div/div[2]/div[contains(@class, 'olpOffer')]", array(
            'offer.price' => "div[1]/span[contains(@class, 'olpOfferPrice')]",
            'offer.price-shipping' => "div[1]/p/span/span[@class='olpShippingPrice']",
            'seller.name' => "div[3]/p[contains(@class, 'olpSellerName')]/span/a",
            'seller.logo' => "div[3]/p[contains(@class, 'olpSellerName')]/a/img/@src|div[3]/p[contains(@class, 'olpSellerName')]/span/a/img/@src",
            'seller.aboutus' => "div[3]/p[contains(@class, 'olpSellerName')]/a/@href|div[3]/p[contains(@class, 'olpSellerName')]/span/a/@href",
            'seller.department_url' => "div[3]/p[contains(@class, 'olpSellerName')]/a/@href",
        ));
        foreach ($result as &$row) {
			// add the image url
			$row['product.image'] = $imageUrl;
			
			// assure URL
			if(strpos($row['seller.aboutus'],'/')==0){
				$row['seller.aboutus'] = 'http://amazon.com'.$row['seller.aboutus'];
			}
			//extract merchant id somehow
			$merchantId = "";
			if (strpos($row['seller.aboutus'],'redirect')!==false) {
				// if aboutus URL has redirect, it is for a new seller with no reviews
				$merchantId = $this->extractUriParam('merchantID', $row['seller.aboutus']);
			}
			if(empty($merchantId)){
				$merchantId = $this->extractUriParam('seller', $row['seller.aboutus']);
			}
			if(empty($merchantId)){
				preg_match('|.*/shops/([^/]+)|', $row['seller.aboutus'], $matches);
				if(!empty($matches[1]))
					$merchantId = $matches[1];
			}
			// use the dependable page if we can
			if(!empty($merchantId)){
				$row['seller.aboutus'] = "http://www.amazon.com/gp/aag/main?ie=UTF8&seller=".$merchantId;
				$row['seller.seller_id'] = $merchantId;
			}
			// Seller Name is mandatory data.  Crawl an extra page if we must
			if(empty($row['seller.name'])){
				if(!empty($merchantId)){
					$xpath_helper = $this->openHref($row['seller.aboutus']);
					$row['seller.name'] = $xpath_helper->queryValue("//h1");
				}
				// follow the seller image link as a last resort
				if(empty($row['seller.name'])){
					$xpath_helper = $this->openHref($row['seller.department_url']);
					$row['seller.name'] = $xpath_helper->queryValue("//*[@id='s-result-count']/span|//title|//h1");
					$row['seller.name'] = preg_replace('/ Storefront$/', '', $row['seller.name']);
					$row['seller.name'] = preg_replace('/: Online Shopping for Electronics, Apparel, Computers, Books, DVDs & more/', '', $row['seller.name']);
				}
			}
            unset($row['seller.department_url']);
        }
        return $result;
    }
    
    public function parseOfferMerchantListPagination(XPathHelper $xph)
    {
        return $xph->queryValue("//ul[@class='a-pagination']/li[@class='a-last']/a/@href");
    }
    
    public function parsePageType(XPathHelper $xph)
    {
        if (null !== $xph->xpQuery("//*[@id='noResultsTitle']"))
            return "searchResult";
        if (null !== $xph->xpQuery("//*[@id='s-result-count']"))
            return "searchResult";
        if (null !== $xph->xpQuery("//*[@id='atfResults']"))
            return "searchResult";
        if (null !== $xph->xpQuery("//div[@id='product-title_feature_div']/div[@class='buying'] | //form[@id='handleBuy']/div[@class='buying']"))
            return "product";
		// TODO: fix this
		//if ('Robot Check' == $xph->queryValue("//html/body/title/text()", 1))
			return "captcha";
		// default, save for analysis
		$ci =& get_instance();
		$html_contents = "\n\n----------------------------------------------------------\n".date('Y-m-d H:i:s')."\n".$xph->dump();
		file_put_contents( $ci->config->item('file_root') . "output.amazon.failed.parse.txt", $html_contents, FILE_APPEND );
        throw new Exception(__FUNCTION__ . ": unexpected page type at " . $xph->__toString());
    }

    //REFACT: expand as an amazon parse_url which return a hastable of host,type, asin,titleseo
    public function GetUrlType($url)
    {
        $p = parse_url($url);
        $this->assertEquals('www.amazon.com', $p['host']);
        $this->assertEquals(1, preg_match("#^/(?<type2>s)/|^/(?<seotitle>[^/]+)/(?<type>[^/]*)/(?<asin>[A-Z0-9]+)/#", $p['path'], $matches));

        if ($matches['type2'] === 's')
            return 'search';

        switch ($matches['type'])
        {
            case 'dp':
                return 'product';
            case 'e':
                return 'brand';
            default:
                throw new Exception(__FUNCTION__ . ":unexpected url type $type");
        }
    }

    //INFO: naive implementation to avoid dependency on PECL HTTP
    private function http_build_url($parts)
    {
        return
                ( isset($parts['scheme']) ? $parts['scheme'] . ":" : "" )
                . ( isset($parts['host']) ? '//' . $parts['host'] : "" )
                . $parts['path']
                . ( isset($parts['query']) ? "?" . $parts['query'] : "" )
                . ( isset($parts['fragment']) ? "#" . $parts['fragment'] : "" )
        ;
    }

    public function href2url($href, $pageUrl = null)
    {
        $url = parent::href2url($href, $pageUrl);
        $parsed = parse_url($url);

        //TRICK: $query is the "return value" of parse_str
        if (!isset($parsed['query']))
            return $url;

        parse_str($parsed['query'], $query);
        unset($query['qid']);
        $parsed['query'] = http_build_query($query);

        return $this->http_build_url($parsed);
    }

    public function translateRecord($record)
    {
        $out = array();

        if (isset($record['_spider.status']))
            switch ($record['_spider.status'])
            {
                case 'complete': unset($record['_spider.status']);
                    break;
                case 'incomplete': return array();
                    break;
                default:
                    throw new Exception("unsupported _spider.status: " . $record['_spider.status']);
            };
        foreach ($record as $key => &$value)
        {
            switch ($key)
            {
                /* 				case 'product.price':
                  if($value !==null)
                  $out[$key]=$value;
                  break;
                 */
                case 'amazon.sellers-boxed.product.price_hidden':
                    //TRICK: this is just a control field used to accept missing prices.
                    break;
                case 'product.price_listed':
                    if ($value !== null)
                    {
                        $out[$key] = $value;
                        $this->assertEquals(false, isset($record['amazon.sellers-boxed.product.price_hidden']) && $record['amazon.sellers-boxed.product.price_hidden'] === null);
                        break;
                    }
                    //INFO: we accept no price only when the product is not available.
                    if (isset($record['product.availability']) && (
                            $record['product.availability'] === 'Currently unavailable' || $record['product.availability'] === 'Currently unavailable.' || $record['product.availability'] === 'Sign up to be notified when this item becomes available.') || isset($record['amazon.product.category']) && $record['amazon.product.category'] === 'Kindle Store:' || isset($record['amazon.sellers-boxed.product.price_hidden']) && $record['amazon.sellers-boxed.product.price_hidden'] === 'To see our price, add this item to your cart. You can always remove it later. Why don\'t we show the price?' || isset($record['product.condition_and_sellers_count']) || isset($record['product.condition'])
                    )
                    {
                        //FIXME: default value for API compatibility
                        $out[$key] = 'NULL';
                        break;
                    }
                    //INFO: when selling price not available, will take the new condition price. 
                    //TRICK: the product.percondition must have been translated before product.price_listed					
                    if (!( isset($out['product.sellers-percondition']) && count($out['product.sellers-percondition']) !== 0 || isset($out['product.sellers']) && count($out['product.sellers']) !== 0
                            ))
                    {
                        throw new Exception("$key is mandatory... And should not be null " . var_export($record, true));
                        break;
                    }
                    if (isset($out['product.sellers-percondition']['new']))
                    {
                        $out[$key] = $out['product.sellers-percondition']['new']['product.price_listed'];
                        break;
                    }
                    if (isset($out['product.sellers']['product.price_listed']))
                    {
                        $out[$key] = $out['product.sellers-percondition']['product.price_listed'];
                        break;
                    }
                    if (count($out['product.sellers-percondition']) !== 0)
                    {
                        $out[$key] = 'only ' . reset(array_keys($out['product.sellers-percondition']));
                        break;
                    }
                    break;

                case 'product.price_retail':
                    if ($value === null)
                        $out[$key] = $record['product.price_retail'];
                    $out[$key] = $value;
                    break;

                case 'product.href-local2':
                case 'product.href-local':
                    //at least 1 url is mandatory
                    $this->assertEquals(false, isset($record['product.href-local']) && $record['product.href-local'] === null && isset($record['product.href-local2']) && $record['product.href-local2'] === null);
                    //skip when current product url is null
                    if ($value === null)
                        break;
                    //when 2 product urls, double check they are the same  
                    if (isset($out['product.url']))
                        $this->assertEquals($out['product.url'], $this->href2url($value));
                    $out['product.url'] = $this->href2url($value);
                    //INFO: extract sku (i.e. ASIN)from URL
                    //REFACT: transform getUrlType into parseUrl and reuse here to extract sku.
                    $sku = $this->extract_preg("#/[^/]+/dp/(?<value>[A-Z0-9]+)/#", $value);
                    if (!isset($record['product.sku']))
                        $out['product.sku'] = $sku;
                    else
                    #//FIXME: some product may have a different SKU in the product link and the new offers link
                    #//e.g. http://www.amazon.com/Nutrition-Now-Pro-Biotic-Acidophilus-Multi-Pack/dp/B002LM5AP0/ref=sr_1_3?ie=UTF8&qid=1393753959&sr=8-3&keywords=027917001128
                    #$this->assertEquals($record['product.sku'],$sku);
                        break;
                case 'brand.href-local':
                    $out['brand.url'] = $this->href2url($value);
                    break;
                case 'seller.href-local':
                    $out['seller.url'] = $this->href2url($value);
                    break;
                case 'sellers.href-local':
                    $out['sellers.url'] = $this->href2url($value);
                    break;
                case'product.numeric_details[]':
                    $out['product.weight'] = $this->extract_preg("/^Weight: (?<value>.*)$/", $value[3]);
                    break;
                case 'product.title':
                    $out['product.name'] = $value; // patch for api_stickybusiness
                case 'product.name':
                case 'product.upc':
                case 'product.description_preview':
                case 'product.description':
                case 'product.rating':
                case 'product.quantity':
                case 'product.reference':
                case 'product.weight':
                case 'product.likes-count':
                case 'amazon.product.actualPriceExtraMessaging':
                case 'amazon.session.id':
                case 'product.sku':
                case 'amazon.merchantexclusive':
                case 'seller.id':
                case 'amazon.node':
                case 'amazon.listing-id':
                case 'amazon.sellingcustomer.id':
                case 'product.availability':
                case 'seller.name':
                case 'seller.reviews-score-avg':
                case 'seller.reviews-count':
                case 'amazon.seller.shipping-destinations':
                    $out[$key] = $value;
                    break;
                case 'seller.name2':
                    if ($value !== null)
                        $this->assertEquals($value, $out['seller.name']);
                    break;
                case 'brand.name':
                    if ($value === null || $value === '')
                        break;
                    $out[$key] = $value;

                case 'amazon.product.starring':
                    if ($value === null)
                        break;
                    //TRICK: see product.brand_by for actual implementation
                    break;
                case 'brand.href-local':
                case 'brand.name_link':
                    break;
                case 'brand.name_andall':
                    if ($value === null)
                    {
                        //FIXME: we have no brand name anymore when no link
                        $out['brand.name'] = null;
                        if ($record['brand.name'] !== null)
                            throw new Exception("unexpected combination " . $record['brand.name']);
                        break;
                    }
                    $this->assertEquals(1, preg_match(
                                    "#^"
                                    . "((by|Starring) ((?U)(?<brand>(\(editor\)|[^\(\)\-]|[A-Za-z]\-[A-Za-z ])+))(?U) ?)?"
                                    . "(\((?<parentheses>[^\(\)]*)\))?"
                                    . "( \- (?<format>[a-zA-Z,; ]*))?"
                                    . "\(?"
                                    . "$#"
                                    , $value, $matches));

                    //TRICK: with real data amazon.product.starring/brand.name is always set but this make unit test less verbose.
                    if (isset($record['amazon.product.starring']) && $record['amazon.product.starring'] !== null)
                    //INFO: starring contains "Starring " header
                        $this->assert(false !== strpos($record['amazon.product.starring'], $matches['brand']));
                    if (isset($record['brand.name']) && $record['brand.name'] !== null)
                    //INFO: brand.name does not contain "by " header
                        $this->assert(false !== strpos($matches['brand'], $record['brand.name']));
                    if (isset($matches['brand']) && $matches['brand'] !== '')
                        $out['brand.name'] = $matches['brand'];
                    //FIXME: split brand name into a list when multiple author e.g. "Zadie Smith, Karen Bryson and Don Gilet"
                    if (isset($matches['parentheses']))
                    {
                        $submatches = array();
                        if (!preg_match("#^(?<value>Formerly (?<formerly>.*)|((?<format>[^\(\)\-]+) -)?(?<date>([A-Z][a-z]{2} [0-9]+\, )?[0-9]{4}))$#", $matches['parentheses'], $submatches))
                            $submatches['format'] = $matches['parentheses'];
                        if (isset($submatches['formerly']) && $submatches['formerly'] !== '')
                            $out['brand.name_former'] = $submatches['formerly'];
                        $this->assertEquals(false, false);
                        if (isset($matches['format']) &&
                                ($submatches['format'] !== null && $submatches['format'] !== '' ))
                        {
                            throw new Exception("duplicate format information");
                        }
                        if (isset($submatches['format']) && $submatches['format'] !== null && $submatches['format'] !== '')
                        {
                            $matches['format'] = $submatches['format'];
                        }

                        if (isset($submatches['date']))
                        {
                            if (strlen($submatches['date']) === 4) //i.e. it's a year number
                            {
                                $out['product.release-date'] = $submatches['date'];
                            }
                            else
                            {
                                $tmp = new DateTime($submatches['date']);
                                $out['product.release-date'] = $tmp->format('Y-m-d');
                            }
                        }
                    }
                    if (isset($matches['format']))
                    {
                        //INFO: there are list of format keywords in documentations such as http://www.amazon.com/gp/feature.html/ref=cd_format_pop?ie=UTF8&docId=570000
                        //INFO: but they are very incomplete
                        if (false === array_search($matches['format'], explode("|", "Maxi|UK Import|Unabridged|Large Print|VHS|VHS Tape|Video Game|Verified Gluten Free")))
                        {
                            //TRICK:through exception to crash when discovering new cases for additional.
                            echo ("\n####Unexpected format value " . var_export($matches['format'], true) . "###\n");
                        }

                        $out['amazon.product.format'] = $matches['format'];
                        break;
                    }
                    break;
                case 'amazon.product.category':
                    if (null === $value)
                        break;
                    $out[$key] = $this->extract_preg("#^(?<value>[A-Z].*[a-zA-Z]+):$#", $value);
                    break;
                case 'amazon.product.category-count':
                    if (null === $value)
                        break;
                    $out[$key] = $this->extract_preg("#^See all ((?<value>[0-9]+(,[0-9]+)*) )?items$#", $value);
                    break;
                case 'sellers.href-local':
                    $out['sellers.url'] = $this->href2url($value);
                    break;
                case 'product.sellers':
                case 'product.sellers-percondition':
                    $out[$key] = array();
                    foreach ($value as $tmpKey => $tmpValue)
                    {
                        //if (!isset($tmpValue['product.price_listed'])){
                        //    var_export($tmpValue);
                        //    //		die('sdsd');
                        //}
                        //if (isset($tmpValue['product.price_listed']) && empty( $tmpValue['product.price_listed'])){
                        //    unset($tmpValue['product.price_listed']);
                        //    die("prout");
                        //}
                        $tmpValue = $this->translateRecord($tmpValue);
                        $out[$key][$tmpValue['product.condition']] = $tmpValue;
                    }
                    break;
                case 'product.condition_and_sellers_count':
                    $out['product.condition'] = $this->extract_preg('#^(?<count>[0-9]+)\s+(?<value>[a-z\&\s]*)$#u', $value, $matches);
                    $out['sellers-count'] = $matches['count'];
                    break;
                case 'product.condition':
                    $this->assertEquals(true, false !== array_search($value, explode("|", "new|New|used|Used|used & new|open box")));
                    $value = str_replace("used & new", "any", $value);
                    $out[$key] = strtolower($value);
                    break;
                case 'sellers-count':
                    $out[$key] = $this->extract_preg("#^\(?(?<value>[0-9]*) (offers\))?#", $value);
                    break;
                case 'product.reviews-count':
                    if ($value === null)
                        break;
                    $out[$key] = $this->extract_preg("#^(?<value>\d{1,3}(,\d{3})*)( customer reviews?)?$#u", $value);
                    break;
                case 'product.reviews-score-avg':
                    if ($value === null)
                        break;
                    $out[$key] = $this->extract_preg("#^(?<value>\d\.\d) out of 5 stars\s*$#u", $value);
                    break;
// ------- START substructures with named items -------	
                case 'product.variation-selection':
                case 'product.description':
                    if (!is_array($value))
                        throw new Exception("unexpected value " . var_export($value, true));
                    $out[$key] = $this->translateRecord($value);
                    break;
                case 'product.variation.name':
                case 'product.variation.id':

                case 'product.variation.title':
                case 'product.variation.id-css':
                case 'product.variation.image-css':

                case 'condition+count':
                case 'price':
                    $out[$key] = $value;
                    break;
// ------- END substructures with named items -------
// ------- START substructures with numbered substrucures -------
                case 'amazon.product.sales-outbound':
                case 'amazon.product.sales-related':
                case 'amazon.sellers-boxed':
                    foreach ($value as &$item)
                        $item = $this->translateRecord($item);
                    $out[$key] = $value;
                    break;
// ------- END substructures with numbered substrucures -------
// ------- START substructures with numbered items -------
                case 'product.features':
                    $out[$key] = implode("|", $value);
                    break;
// ------- END substructures with numbered items -------
// ------- START substructures with names as value -------
                case 'product.variation-option':
                case 'product.variation-selection2':
                case 'product.details':
                case 'amazon.product.additional_information':
                    $out[$key] = array();
                    foreach ($value as $item)
                        $out[$key][$item['@label']] = $item['@value'];
                    break;
                case 'amazon.offer.supersaver':
                    break;
                case 'offer.price-shipping':
                    $key = 'product.price-shipping';
                    if (isset($record['amazon.offer.supersaver']) && ( $value === null || $value === 'Eligible for'))
                        $value = '+ Free shipping';
                    $value = $this->extract_preg('#^\s{0,1}\+\s*(?<value>(Free|\$[0-9]+\.[0-9]{2}))\s?(for standard )?[sS]hipping$#u', $value);
                    if ($value === 'Free')
                        $out[$key] = '$0.00';
                    else
                        $out[$key] = $value;
                    break;
                case 'product.order._script':
                    //TRICK: ignore , this field is beeing used for the sole purpose of parsing product.order._fullfilment_and_availability_and_seller_and_wrap 
                    break;
                case 'product.order._fullfilment_and_availability_and_seller_and_wrap':
                    if ($value === 'Available from these sellers.')
                    {
                        $out['product.order.fullfilment'] = 'seller';
                        $out['seller.name'] = null; //INFO: we don't want to report "These sellers" as a seller.name;
                        break;
                    }
                    if ($value === "Currently unavailable.We don't know when or if this item will be back in stock.")
                    {
                        $out['product.order.fullfilment'] = null;
                        $out['seller.name'] = null; //INFO: we don't want to report "These sellers" as a seller.name;
                        break;
                    }
                    $value = str_replace($record['product.order._script'], '', $value);
                    $record['product.order.fullfilment'] = $this->extract_preg(array(
                        "#^" . preg_quote($record['product.availability'])
                        . "\s?Sold by " . preg_quote($record['seller.name']) . "\s+and Fulfilled by\s+(?<value>Amazon.?)\s+"
                        . "\s*((?<wrap>Gift-wrap available)\.)?"
                        . "$#mu",
                        "#^" . preg_quote($record['product.availability'])
                        . "(Processing takes an additional 4 to 5 days for orders from this seller\. )?"
                        . "(.*?Ships from and sold by)\s*(?U)(?<value>.*)\.?"
                        . " *((?<wrap>Gift-wrap available)\.)?"
                        . "$#mu",
                            ), $value, $matches);
                    if (isset($matches['wrap']))
                        $record['amazon.order.wrap'] = $matches['wrap'];
                    break;
                case 'amazon.order.wrap':
                    switch (my_mb_trim($value))
                    {
                        case 'Gift-wrap available':
                        case 'Gift-wrap available.':
                        case '. Gift-wrap available.':
                            $out[$key] = true;
                            break;
                        case '.':
                            $out[$key] = false;
                            break;
                        case null:
                        case '':
                            $out[$key] = null;
                            break;
                        default:
                            throw new Exception("unexpected value for $key '$value'" . var_export($record, true));
                    }
                    break;

                case 'product.order.fullfilment':
                    if ($value === 'Fulfilled by Amazon')
                    {
                        $out[$key] = 'market';
                        break;
                    }
                    elseif ($value = $record['seller.name'])
                    {
                        $out[$key] = 'seller';
                        break;
                    }
                    if ($record['product.price_listed'] === null && ( $record['product.availability'] === 'Currently unavailable.' || $record['product.availability'] === 'Available from these sellers.'
                            )
                    )
                    {
                        break;
                    }

                    throw new Exception("Unexpected $key : '$value'\n" . substr(var_export($record, true), 0, 1000));
                    break;
                case 'product.price-shipping_and_fullfilment':
                    if (my_mb_trim($value) === "& this item ships for FREE with Super Saver Shipping.



Details")
                    {
                        $out['product.price-shipping'] = '$0.00';
                        $out['product.order.fullfilment'] = 'market';
                    }
                    else
                    {
                        $out['product.price-shipping'] = $value;
                        $out['product.order.fullfilment'] = 'seller';
                    }
                    break;
                case 'product.image-tiny':
                case 'product.image-small':


                    /* 					'product.title' => "a",
                      'product.href-local' => "a/@href",
                      'product.brand' => "span[@class='vtp-binding-byline']",
                      'product.price_listed' => "div[@class='price']",
                      'product.sku' => "span[@class='rating-stars']/span[@class='crAvgStars']/span[@class='asinReviewsSummary']/@name",
                      'product.reviews-count' => "span[@class='rating-stars']/span[@class='crAvgStars']/a",
                      'product.reviews-score-avg' => "span[@class='rating-stars']/span[@class='crAvgStars']/span[@class='asinReviewsSummary']/a",
                      ));

                      'seller.name' => "tr[@class='mbcMerch']/td[1]",
                      'product.price_listed' => "tr/td[@class='mbcPriceCell']/text()",
                      'product.price-shipping' => "tr/td[@class='mbcPriceCell']/span[@class='plusShippingText']",
                     */

                    break;

//-----------------------------------------------------------------------------
// Edge cases
                case 'amazon.product.format':
                    $out[$key] = $value;
                    break;
//-----------------------------------------------------------------------------
///----- offers START ---------------------------------------------------------
                case 'offers.condition':
                    switch ($value)
                    {
                        case 'bucketnew':
                            $out['product.condition'] = 'new';
                            break;
                        case 'bucketused':
                            $out['product.condition'] = 'used';
                            break;
                        default:
                            throw new Exception("unexpected $key : $value");
                    }
                    break;
                case 'offers.sellers':
                    $out[$key] = array();
                    foreach ($value as $item_key => $item_value)
                        $out[$key][$item_key] = $this->translateRecord($item_value);
                    break;
///----- offers END -----------------------------------------------------------

                default:
                    throw new Exception("Unexpected $key : '" . substr(var_export($value, true), 0, 1000) . "'");
                    break;
            }
        };
        return $out;
    }

}
