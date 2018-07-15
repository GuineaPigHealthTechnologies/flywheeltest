<!-- 1. Find Business -->
<div class="form-group">
    <div class="col-sm-12">
        <h4 class="text-left"><span class="yrw-step">1</span><?php echo yrp_i('Find Business'); ?></h4>
        <input type="text" class="yrw-term form-control" value="" placeholder="Search Term (e.g. 'Starbucks', 'restaurants')" />
    </div>
</div>
<div class="form-group">
    <div class="col-sm-12">
        <input type="text" class="yrw-location form-control" value="" placeholder="Location (address, neighborhood, city, state or zip)" />
    </div>
</div>
<div class="form-group">
    <div class="col-sm-12">
        <button class="yrw-search-business btn btn-block btn-primary"><?php echo yrp_i('Search Business'); ?></button>
    </div>
</div>
<!-- 2. Select Business -->
<div class="form-group">
    <div class="col-sm-12">
        <h4 class="text-left"><span class="yrw-step">2</span><?php echo yrp_i('Select Business'); ?></h4>
        <div class="yrw-businesses"></div>
    </div>
</div>
<!-- 3. Save Reviews -->
<div class="form-group">
    <div class="col-sm-12">
        <h4 class="text-left"><span class="yrw-step">3</span><?php echo yrp_i('Save Reviews'); ?></h4>
        <div class="yrw-reviews"></div>
        <div class="yrw-three-reviews-note" style="display:none"><?php echo yrp_i('Yelp can return 3 reviews only'); ?></div>
        <div class="yrw-save-reviews-container"></div>
    </div>
</div>