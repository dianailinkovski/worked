<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Product Catalog</strong>
        </h3>
    </div>
    
    <div class="panel-body">

         <!-- custom container start -->
         <p>Use the filter below to find specific catalog items. Clicking on an item will allow you to edit additional item settings.</p>
     
         <div class="tabs bigTabs clear">
            <ul class="tabNav clear">
                <li><div class="tabCornerL"></div><a href="#catalogList" id="catalogListTab" class="tabItem" onclick="return false;">Product List</a></li>
                <!-- <li><div class="tabCornerL"></div><a href="#promotionalPricing" id="promotionalPricingTab" class="tabItem" onclick="return false;">Promotional Pricing</a></li> -->
                <li><div class="tabCornerL"></div><a href="#productGroups" id="productGroupsTab" class="tabItem" onclick="return false;">Product Groups</a></li>
                <!-- <li><div class="tabCornerL"></div><a href="#competitorAnalysis" id="competitorAnalysisTab" class="tabItem" onclick="return false;">Competitor Analysis</a></li> -->
                
                    <li><div class="tabCornerL"></div><a href="#productLookup" id="productLookupTab" class="tabItem" onclick="return false;">Lookup</a></li>
                
            </ul>
 
             <section id="catalogList" class="tabContent"><?= $this->load->view('catalog/_catalog_list', '', TRUE) ?></section>
            <section id="promotionalPricing" class="tabContent ui-tabs-hide"><?= $this->load->view('catalog/_promotional_pricing', '', TRUE) ?></section>
            <section id="productGroups" class="tabContent ui-tabs-hide"><?= $this->load->view('catalog/_product_groups', '', TRUE) ?></section>
            <!-- <section id="competitorAnalysis" class="tabContent ui-tabs-hide"><? //= $this->load->view('catalog/_competitor_analysis', '', TRUE) ?></section> -->
            
                <section id="productLookup" class="tabContent ui-tabs-hide"><? $this->load->view('catalog/_product_lookup', '', TRUE) ?></section> 
          
     
             <div id="addProdPopup" class="modalWindow dialog">
                 <div class="dlg-content">
                 </div>


            </div><!-- end #addProdPopup -->
        </div>
        
    </div>
</div>        