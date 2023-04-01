<?php
require_once 'api_stickybusiness.php';

class ProductFinderException extends Exception{};

class ProductFinderData
{
	protected function assignAttributes($target,$values,$names,$mandatory=true)
	{
		foreach($names as $attribute)
		{
			if(isset($values[$attribute]) )
			{
				$attribute2=str_replace(".","_",$attribute);
				$target->$attribute2=$values[$attribute];
				unset($values[$attribute]);
				continue;
			}
			if( $mandatory )
				continue;//TODO: report error status ? exception ?
		}
	}
}

class ProductFinderRequest extends ProductFinderData
{
	public $product_upc; ///@string
	public $name; ///@string
	public $brand; ///@string
	public $partnumber; ///@string
	
	public function __construct($upc,$name=null,$brand=null,$partnumber=null)
	{
		if( !preg_match("/[0-9]{9}/",$upc) and $partnumber == null )
			throw new ProductFinderException(__CLASS__ .'.'.__FUNCTION__.": unexpected UPC value '$upc'");
		$this->product_upc=$upc;
		$this->name=$name;
		$this->brand=$brand;
		$this->partnumber=$partnumber;
	}
}

class ProductFinderResponse extends ProductFinderData
{
	private $_request;
	private $_data;
	public $timestamp;
	public $product_name;
	public $product_price_listed;
	public $product_url;
	public $merchant_url;
	public $product_image_url;
	public $product_sku;
	
	public function __construct(ProductFinderRequest $request,$merchantUrl,$response)
	{
		$this->timestamp= $response['timestamp'];
	
		$this->assignAttributes( $this,$response,
			explode("|","timestamp|product.name|product.price_listed|product.url|merchant.url|product.image_url" ));
		$this->assignAttributes( $this,$response, 
			explode("|","seller.id|seller.name|seller.url|product.sku|product.price_retail|product.brand|product.upc|extra.attributes_merchant"),
			false );
		$this->_request=$request;
		$this->_data=$response;
	}

	public  function __get($name)
	{
		if( isset($this->_data['product.'.$name]) )
			return $this->_data['product.'.$name];
		return $this->_request->$name;
	}
	
	public function getTimestamp()
	{
		return $this->timestamp;
	}
	
	public function voidTimestamp()
	{
		//FIXME should throw exception if not running from PHPUnit
		$this->timestamp=null;
		$this->_data['timestamp']=null;
	}
}

class ProductFinder
{
	public function search(ProductFinderRequest $request,$merchantUrl=null)
	{
		$spider= new StickyBusiness();
		
		if($merchantUrl=== null)
		{
			$response=array();
			foreach($spider->getSpidersList() as $merchantDomain)
			{
				$response=array_merge($response,$this->search($request,"http://".$merchantDomain));
			}
			return $response;
		}

		if( $merchantUrl == 'http://www.amazon.com' )
		  $marketplaceSpider= new Spider_AmazonCom_controller();
        elseif ($merchantUrl == 'http://www.google.com')
          $marketplaceSpider= new Spider_GoogleCom_controller();

        if($request->product_upc != null)
            $searchResults = $spider->searchUpc($request->product_upc,$merchantUrl);
        else
            $searchResults = $spider->search(implode(" ",array($request->brand, $request->partnumber)), $merchantUrl);

        return $this->formatSearchResults($searchResults, $request,$merchantUrl,$marketplaceSpider);
    }

    private function formatSearchResults($searchResults, $request,$merchantUrl,$marketplaceSpider)
    {
		$response=array();
        foreach($searchResults as $productResponse)
		{
			$product_details=new ProductFinderResponse($request , $merchantUrl, $productResponse );
			//REFACT: hardcoded implementation for amazon marketplace.
			if( $merchantUrl !== 'http://www.amazon.com' and $merchantUrl != 'http://www.google.com' )
            {
                $response[]=$product_details;
				continue;
            }
			$offers=$marketplaceSpider->getProductOffers($product_details->product_url);
			var_dump($offers);
			foreach($offers as $offer)
			{
				$productResponse['seller.name']=$offer['seller.name'];
				$productResponse['seller.url']=$offer['seller.url'];
				$productResponse['product.price_listed']=$offer['product.price_listed'];
				$response[]= new ProductFinderResponse($request , $merchantUrl , $productResponse);
			}
            if( count($offers) == 0)
                $response[]= $product_details;
		}
		return $response;
	}
}
?>
