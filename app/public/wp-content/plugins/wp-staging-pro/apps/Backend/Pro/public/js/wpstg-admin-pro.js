"use strict";

var WPStagingPro = function ($) {

    var that = {
        isCancelled: false,
        isFinished: false,
        getLogs: false
    };

    // Cache Elements
    var cache = {elements: []};

    /**
     * Get / Set Cache for Selector
     * @param {String} selector
     * @returns {*}
     */
    cache.get = function (selector)
    {
        // It is already cached!
        if ($.inArray(selector, cache.elements) !== -1)
        {
            //console.log('already cached' + cache.elements[selector]);
            return cache.elements[selector];
        }

        // Create cache and return
        cache.elements[selector] = jQuery(selector);

        //console.log(cache.elements[selector]);
        return cache.elements[selector];
    };

    /**
     * Refreshes given cache
     * @param {String} selector
     */
    cache.refresh = function (selector)
    {
        selector.elements[selector] = jQuery(selector);
    };
    
    
    /**
     * Ajax Scanning before starting push process
     */
    var startScanning = function ()
    {
        // Scan db and file system
        console.log('Loading WP Staging Pro Initially');
        var $workFlow = cache.get("#wpstg-workflow");
        

        $workFlow
                // Load scanning data
                .on("click", ".wpstg-push-changes", function (e) {
                    e.preventDefault();

                    var $this = $(this)

                    // Disable button
                    if ($this.attr("disabled"))
                    {
                        return false;
                    }

                    // Add loading overlay
                    $workFlow.addClass("loading");
                    
                    // Get clone id
                    //var cloneID = $this.data("clone");
                                // Get clone id
                    var cloneID = $(this).data("clone");
                    console.log('Clone ID: ' + cloneID);

                    // Prepare data
                    that.data = {
                        action: 'wpstg_scan',
                        clone: cloneID,
                        nonce: wpstg.nonce
                    };

                    // Send ajax request
                    WPStaging.ajax(
                            that.data,
                            function (response) {

                                if (response.length < 1)
                                {
                                    showError("Something went wrong, please try again");
                                }

                                // Styling of elements
                                $workFlow.removeClass("loading").html(response);

                                cache.get(".wpstg-current-step")
                                        .removeClass("wpstg-current-step")
                                        .next("li")
                                        .addClass("wpstg-current-step");
                                
                                cache.get("#wpstg-loader").hide();
                                cache.get(".wpstg-loader").hide();
                                
                            },
                            "HTML"
                            );
                })
                // Previous Button
                .on("click", ".wpstg-prev-step-link", function (e) {
                    e.preventDefault();
                    WPStaging.loadOverview();
                });
    };


    // Start the whole pushing process
    var startProcess = function () {
        var $workFlow = cache.get("#wpstg-workflow");

        // Click push changes button
        $workFlow.on("click", "#wpstg-push-changes", function (e) {
            e.preventDefault();

            // Hide db tables and folder selection
            cache.get('#wpstg-scanning-db').hide();
            cache.get('#wpstg-scanning-files').hide();

            // Show confirmation modal
            var cloneID = cache.get('#wpstg-push-changes').data("clone");
            var text = "Are you sure?\n\nThis will overwrite the live site and its plugins, themes and media folder with modified files from the staging site: \"" + cloneID + "\".  \n\nThe database tables will be overwritten if db tables are not explicetely excluded.";

            if (confirmModal(text)) {
                cache.get('#wpstg-push-changes').attr('disabled', true);
                cache.get('.wpstg-prev-step-link').attr('disabled', true);
                cache.get('#wpstg-scanning-files').hide();
                processing();
            }
        });
    }
    
    /**
     * Start ajax processing
     * @returns string
     */
    var processing = function () {
            console.log("Start ajax processing");
            
            // Show loader gif
            cache.get("#wpstg-loader").show();
            cache.get(".wpstg-loader").show();
            
            // Show logging window
            cache.get('#wpstg-log-details').show();
            
            // Get clone id
            var cloneID = cache.get('#wpstg-push-changes').data("clone");
           console.log(cloneID);

            WPStaging.ajax(
                    {
                        action: "wpstg_push_processing",
                        nonce: wpstg.nonce,
                        clone: cloneID,
                        excludedTables: getExcludedTables(),
                        includedDirectories: getIncludedDirectories(),
                        excludedDirectories: getExcludedDirectories(),
                        extraDirectories: getIncludedExtraDirectories()
                    },
            function (response)
            {
                // Undefined Error
                if (false === response)
                {
                    showError("Unknown Error, please try again");
                    cache.get("#wpstg-loader").hide();
                    cache.get(".wpstg-loader").hide();
                    return;
                }
                
                // Throw Error
                if ("undefined" !== typeof(response.error) && response.error){
                    console.log(response.message);
                    WPStaging.showError(response.message);
                    return;
                }
                
                // Add Log messages
                if ("undefined" !== typeof (response.last_msg) && response.last_msg)
                {
                    WPStaging.getLogs(response.last_msg);
                }
                
                // Continue processing
                if (false === response.status)
                {
                    setTimeout(function () {
                        console.log('continue processing');
                        cache.get("#wpstg-loader").show();
                        //cache.get(".wpstg-loader").show();
                        processing();
                    }, wpstg.cpuLoad);
                    
                } else if (true === response.status) { 
                    console.log('Processing....');
                    processing(); 
                } else if ('finished' === response.status || ("undefined" !== typeof(response.job_done) && response.job_done)) {
                    isFinished(response);
                }; 
            },
            "json",
            false
        );
    };
    
    
        /**
     * All jobs are finished
     * @param {object} response
     * @returns object
     */
    var isFinished = function(response){
        console.log('result: ' + response);
        console.log('Finishing .... result: ' + response);
        cache.get("#wpstg-loader").text('Finished');
        cache.get("#wpstg-loader").addClass('wpstg-finished');
        cache.get(".wpstg-loader").text('Finished');
        cache.get(".wpstg-loader").addClass('wpstg-finished');
        cache.get('.wpstg-prev-step-link').attr('disabled', false);
    };
  
     /**
     * Get Excluded (Unchecked) Database Tables
     * @returns {Array}
     */
    var getExcludedTables = function ()
    {
        var excludedTables = [];

        $(".wpstg-db-table input:not(:checked)").each(function () {
            excludedTables.push(this.name);
        });

        return excludedTables;
    };
    
 
    
    /**
     * A confirmation modal
     * 
     * @param string
     * @returns {Boolean}
     */
    var confirmModal = function(string){
        var check = confirm(string);
        
        if(check === true){
            return true;
        }else {
            return false;
        }   
    }
    
        /**
     * Get Included Directories
     * @returns {Array}
     */
    var getIncludedDirectories = function ()
    {
        var includedDirectories = [];

        $(".wpstg-dir input:checked").each(function () {
            var $this = $(this);
            if (!$this.parent(".wpstg-dir").parents(".wpstg-dir").children(".wpstg-expand-dirs").hasClass("disabled"))
            {
                includedDirectories.push($this.val());
            }
        });

        return includedDirectories;
    };

    /**
     * Get Excluded Directories
     * @returns {Array}
     */
    var getExcludedDirectories = function ()
    {
        var excludedDirectories = [];

        $(".wpstg-dir input:not(:checked)").each(function () {
            var $this = $(this);
            if (!$this.parent(".wpstg-dir").parents(".wpstg-dir").children(".wpstg-expand-dirs").hasClass("disabled"))
            {
                excludedDirectories.push($this.val());
            }
        });

        return excludedDirectories;
    };

    /**
     * Get Included Extra Directories
     * @returns {Array}
     */
    var getIncludedExtraDirectories = function ()
    {
        var extraDirectories = [];

        if (!$("#wpstg_extraDirectories").val()) {
            return extraDirectories;
        }

        var extraDirectories = $("#wpstg_extraDirectories").val().split(/\r?\n/);
        //console.log(extraDirectories);

        //excludedDirectories.push($this.val());

        return extraDirectories;
    };

    that.init = function () {
        startProcess();
        startScanning();
    }

    return that;

}(jQuery);


console.log(WPStaging);


jQuery(document).ready(function ($) {
    WPStagingPro.init();
});