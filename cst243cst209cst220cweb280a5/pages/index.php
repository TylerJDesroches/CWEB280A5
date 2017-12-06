<?php
session_start();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Image Ranker</title>
    <!--Include knockout and jquery-->
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/knockout/3.4.2/knockout-min.js"></script>

    <script>

        var viewModel = {
            topImages: ko.observableArray(),
            allImages: ko.observableArray(),
            getImages: function ()
            {
                // make the json/ajax call
                $.getJSON('../json/getimages.php', function (jsonImages)
                {
                    // Success function
                    observableAllImages = Array();
                    for (var i = 0; i < jsonImages['allImages'].length; i++) {
                        observableAllImages.push(new observableImage(jsonImages['allImages'][i]));
                    }
                    viewModel.allImages(observableAllImages);

                    observableTopImages = Array();
                    for (var i = 0; i < jsonImages['topImages'].length; i++) {
                        observableTopImages.push(new observableImage(jsonImages['topImages'][i]));
                    }
                    viewModel.topImages(observableTopImages);

                });
            }
        };

        // On document load
        $(function ()
        {
            // Get the images
            viewModel.getImages();
            // Bind the view model object to the DOM
            ko.applyBindings(viewModel);
        });

        // An image object
        function observableImage(jsonObj)
        {
            this.id = ko.observable(jsonObj.id);
            this.path = ko.observable(jsonObj.path);
            this.memId = ko.observable(jsonObj.memId);
            this.caption = ko.observable(jsonObj.caption);
            this.views = ko.observable(jsonObj.views);
            this.approved = ko.observable(jsonObj.approved);
            this.link = 'details.php?id=' + this.id();

        }

    </script>

</head>
<body>
    <h1>Trending</h1>
    <ul data-bind="foreach: topImages">
        <li><a data-bind="attr:{href: link}"><img data-bind="attr:{src: path}" /></a></li>
    </ul>

    <h1>All Images</h1>
    <ul data-bind="foreach: allImages">
        <li>
            <div><a data-bind="attr:{href: link}"><img data-bind="attr:{src: path}" /></a></div>
            <div data-bind="text: caption"></div>
            <div data-bind=""></div>

        </li>
    </ul>
</body>
</html>
