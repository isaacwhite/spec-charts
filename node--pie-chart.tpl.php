<?php

/**
 * @file
 * Bartik's theme implementation to display a node.
 *
 * Available variables:
 * - $title: the (sanitized) title of the node.
 * - $content: An array of node items. Use render($content) to print them all,
 *   or print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $user_picture: The node author's picture from user-picture.tpl.php.
 * - $date: Formatted creation date. Preprocess functions can reformat it by
 *   calling format_date() with the desired parameters on the $created variable.
 * - $name: Themed username of node author output from theme_username().
 * - $node_url: Direct URL of the current node.
 * - $display_submitted: Whether submission information should be displayed.
 * - $submitted: Submission information created from $name and $date during
 *   template_preprocess_node().
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default values can be one or more of the
 *   following:
 *   - node: The current template type; for example, "theming hook".
 *   - node-[type]: The current node type. For example, if the node is a
 *     "Blog entry" it would result in "node-blog". Note that the machine
 *     name will often be in a short form of the human readable label.
 *   - node-teaser: Nodes in teaser form.
 *   - node-preview: Nodes in preview mode.
 *   The following are controlled through the node publishing options.
 *   - node-promoted: Nodes promoted to the front page.
 *   - node-sticky: Nodes ordered above other non-sticky nodes in teaser
 *     listings.
 *   - node-unpublished: Unpublished nodes visible only to administrators.
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 *
 * Other variables:
 * - $node: Full node object. Contains data that may not be safe.
 * - $type: Node type; for example, story, page, blog, etc.
 * - $comment_count: Number of comments attached to the node.
 * - $uid: User ID of the node author.
 * - $created: Time the node was published formatted in Unix timestamp.
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 * - $zebra: Outputs either "even" or "odd". Useful for zebra striping in
 *   teaser listings.
 * - $id: Position of the node. Increments each time it's output.
 *
 * Node status variables:
 * - $view_mode: View mode; for example, "full", "teaser".
 * - $teaser: Flag for the teaser state (shortcut for $view_mode == 'teaser').
 * - $page: Flag for the full page state.
 * - $promote: Flag for front page promotion state.
 * - $sticky: Flags for sticky post setting.
 * - $status: Flag for published status.
 * - $comment: State of comment settings for the node.
 * - $readmore: Flags true if the teaser content of the node cannot hold the
 *   main body content.
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 *
 * Field variables: for each field instance attached to the node a corresponding
 * variable is defined; for example, $node->body becomes $body. When needing to
 * access a field's raw values, developers/themers are strongly encouraged to
 * use these variables. Otherwise they will have to explicitly specify the
 * desired field language; for example, $node->body['en'], thus overriding any
 * language negotiation rule that was previously applied.
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 * @see template_process()
 */
?>
<div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>

<style>

  .region-content {
    width: 616px;
  }
  .graph-spot {
    height:400px;
    box-sizing: border-box;
    position: relative;
    z-index: 2;
    border: 1px solid #d7d7d7;
  }
  .graph-labels {
    width:100%;
    height:50px;
  }
  .glabel {
    display: inline-block;
    text-align: center;
    margin: .5%;
    vertical-align: top;
    font-family: helvetica, sans-serif;
    font-size: .875em;
    text-transform: uppercase;
  }
  .gtitle {
    padding: .5em;
    text-transform: uppercase;
    background-color: black;
    color: white;
    font-family: helvetica, sans-serif;
  }
  .graph-canvas {
    position: absolute;
    width: 100%;
    top:0;
    left:0;
    z-index: 0;
  }
  .graph {
    position: relative;
  }
  svg path {
    fill-opacity: 0;
    cursor: pointer;
    stroke-opacity: 0;

  }
  .graph-spot tspan{
    font-size: 2em;
    /*text-transform: uppercase;*/
    color: #d7d7d7;
    font-family: arial, helvetica;
    dominant-baseline: central;
    font-weight: bold;
  }
</style>
<?php  /*RETRIEVE THE FIELD COLLECTIONS*/
      
      //TODO
      //  -make a function to draw a line from closest edge 
      //   of circle to intersection of label 
      //  -make a function to check for collisions with other
      //   labels


    //retreive field collection ids and extract for retreival
    $fc_fields = field_get_items('node', $node, 'field_value_and_label');
    $ids = array();
    foreach ($fc_fields as $fc_field) {
       $ids[] = $fc_field['value'];
    } //put the ids into an array

    //make a new array of the actual field colleciton entities
    $fc_entities = array();
    for ( $i=0; $i< count($ids); $i++) {
        $fc_entities[]= entity_load('field_collection_item', array($ids[$i]));
    };

    //assign variable to reference nid later
    $nid = $node->nid;
?>
  
<script type="text/javascript">
//we don't want any global variables in case there are multiple graphs, or else all the graph values will be the same.

  $(document).ready(function(){
    //GET VARIABLES FROM PHP
    var GraphTitle = <?php print drupal_json_encode($title); ?>;
    var fieldCollections = <?php print drupal_json_encode($fc_entities); ?>; 
    var fc_ids = <?php print drupal_json_encode($ids); ?>;
    var percentThickness = <?php print drupal_json_encode($node->{'field_thickness_percent_'}['und'][0]['value']); ?>;
    var rotationDeg = <?php print drupal_json_encode($node->{'field_graph_rotation'}['und'][0]['value']); ?>;
      
    //set the value of the graph title object
    var $gTitle = $("<h3 class='gtitle'></h3>").html(GraphTitle);
    //insert the area to render the graph in
    $('#node-<?php print $nid; ?> .graph').append('<div class="graph-spot"></div>').append('<div class="graph-canvas"></div>').append('<div class="graph-labels"></div>').append('<div class="x-axis-label"></div>');
    
    //figure out how tall/wide it can be from the CSS, which is reponsive
    var canvasHeight = $('#node-<?php print $nid; ?> .graph-spot').height();
    var canvasWidth =  $('#node-<?php print $nid; ?> .graph-spot').width();
    
    //calculate maximum radius based on available space
    var graphRadius = -1; //default case to indicate an error
      if (canvasHeight < canvasWidth) {
        graphRadius = Math.floor(canvasHeight/2);
      } else {
        graphRadius = Math.floor(canvasWidth/2);
      }
      
      //leave extra padding based on shortest side
      pieRadius = graphRadius * .8; 
      
      //insert the graph title
      $gTitle.insertBefore($('#node-<?php print $nid; ?> .graph'));
      //initialize Raphael library on the designated area passing width and height
      paper = Raphael($("#node-<?php print $nid; ?> .graph-spot")[0], canvasWidth, canvasHeight);
     
      var values = []; //array to store numbers passed into field collections
      var labels = []; // array to store labels from field collecitons
      var total = 0; //initial value for total amount passed as value, used to calculate percentages

      for (i=0; i<fieldCollections.length; i++) { 
        //iterate over the field collections, 
        //accessing by field collection id
        var rawHeight = fieldCollections[i][fc_ids[i]].field_value.und[0].value;
        var sgLabel = fieldCollections[i][fc_ids[i]].field_label.und[0].safe_value;
        
        values.push(rawHeight);//add the value
        labels.push(sgLabel);//add the label
        
        total += parseFloat(rawHeight);//add the value to running tally of total
      }

    //calculate the percentages that will be used to draw the actual graph
    var percentages = []; //array to hold percent
    for (i=0; i<values.length; i++) {
      percentages.push(values[i]/total);//iterate over values storing percent in decimal form
    }

    var paths = []; //to hold returned path objects
    var labelObjects = []; //to hold returned labelObjects
    var isUsed = 0;//to keep track of how much of the pie we have used already (in decimal percent)
    var xCenter = canvasWidth/2;//x coordinate of the center of the graph, used later
    var yCenter = canvasHeight/2;
    //iterate over the percentages array
    for (i=0; i<percentages.length; i++) {
      var largeArc = 0;//flag to tell whether calculated angle will be larger than 180
      if (percentages[i]>0.5) {
        largeArc = 1;
      } //no else

      //call the drawArc function and store the returned array
      var currentArc = drawArc((canvasWidth/2),graphRadius,pieRadius,rotationDeg,percentages[i],largeArc,isUsed,1,percentThickness);
      //retrieve the color for this object
      var currentColor = fieldCollections[i][fc_ids[i]].field_color.und[0].rgb;

      //add the new arc with the desired properties, and include rollover action
      var raphaelObject = paper.path(currentArc[0]).attr({
        fill: currentColor,
        stroke: "white",
        "stroke-width": 4
      }).mouseover(function () {
              this.stop().animate({"fill-opacity":" 1"}, 200, "<>");
          }).mouseout(function () {
              this.stop().animate({"fill-opacity": "0.75"}, 200, "<>");
          });

      //add the returned object to an array for manipulation and query later
      paths.push(raphaelObject);
      //update used percentage
      isUsed += percentages[i];

      //make the percent value  of current slice human readable
      // with one decimal point
      var roundPercent = Math.round( percentages[i] * 1000 ) / 10;
      
      //add the percentage to the label specified
      var currentLabel = labels[i] + "\n" + roundPercent + "%";
      
      //adjust the alignment of the text based on quadrant map
      
      //  ~ ————— ~
      //  | 4 | 1 |
      //  | ————— |
      //  | 3 | 2 |
      //  ~ ————— ~

      var labelAnchor = 'start'; //default
      if (currentArc[6] > 2) { 
        labelAnchor = 'end';
      }

      //draw the label, with desired location and anchor point
      var raphaelLabel = paper.text(currentArc[4], currentArc[5], currentLabel).attr({'text-anchor': labelAnchor,"font-size": 8});
      
      //add the returned object to an array for manipulation later
      labelObjects.push(raphaelLabel);

      


      //DIAGNOSTIC CIRCLE
      var diagnostic = paper.circle(currentArc[4],currentArc[5],1).attr("fill","#d7d7d7","style","dominant-baseline: hanging;");
      
    }

  //make a set to add all the arcs to
  //well use this for collision detection if we need to
  var setOfPaths = paper.set();
  for (i=0; i<paths.length;i++) {
    setOfPaths.push(paths[i]);
  }

  //see if the graph labels need balancing
  var posCheck = []; //array to store queries of positions and keys of labels

  //some counters to tell how many labels are on each side
  var labelsLeft = 0;
  var labelsRight = 0;

  //loop through the label objects
  for (i=0; i<labelObjects.length; i++ ) {
    var thisBox = labelObjects[i].getBBox(); //get a bounding box
    var labelQuadrant = -1;//default to indicate error
    var query = []; //make an array to store key and x translation
    
      //check whether it is left or right and add the appropriate
      //keys, position, and count information
      labelQuadrant = getQuadrant(thisBox.x,thisBox.y);
      if (labelQuadrant > 2) { //check if it is right or left
        labelsLeft++;
        query.push(xCenter - thisBox.x2);
      } else {
        labelsRight++;
        query.push(xCenter - thisBox.x);
      }
      query.push(i);
      query.push(labelQuadrant);
      query.push(thisBox);//store the bounding box too.
      posCheck.push(query); //add the query to the collection array
  }

  //check if the graph is not balanced
  if (labelsLeft > labelsRight) {
    posCheck.sort(compareLabelPos);//sort the labels by position
    var i = labelsRight; //set starting point to array index number of right labels
    //we have to check that the number isn't equal AND that it is not off by one
    //if we don't, we will loop until the index is undefined on an odd number of labels
    while ((labelsLeft != labelsRight) && (labelsLeft - labelsRight != 1)) {
      //move labels right
      var labelNumber = posCheck[i][1]; //access the label number);
      var toTransform = posCheck[i][0] + (labelObjects[labelNumber].getBBox().width/2);//how much to move the label
      console.log(labelObjects[labelNumber]);
      absTranslate(labelObjects[labelNumber],toTransform,0);
      labelObjects[labelNumber].attr({'text-anchor': 'start'});//adjust the anchor
      var newBBox = labelObjects[labelNumber].getBBox();
      posCheck[i][2] = getQuadrant(newBBox.x,newBBox.y);
      posCheck[i][3] = newBBox;
      labelsRight++;//increment count of right labels
      labelsLeft--;//decrement count of left labels
      i++;//increment index number
    }
  } else if (labelsRight > labelsLeft) {
    //move labels left
    posCheck.sort(compareLabelPos); //sort the labels by position
    var i = labelObjects.length - labelsLeft -1; //start in reverse
    //we have to check that the number isn't equal AND that it is not off by one
    //if we don't, we will loop until the index is undefined on an odd number of labels
    while ((labelsLeft != labelsRight) &&  (labelsRight - labelsLeft != 1)) {
      var labelNumber = posCheck[i][1]; //access the label number);
      var toTransform = posCheck[i][0] - (labelObjects[labelNumber].getBBox().width/2);//how much to move the label
      absTranslate(labelObjects[labelNumber],toTransform,0);
      labelObjects[labelNumber].attr({'text-anchor': 'end'});//adjust the anchor
      var newBBox = labelObjects[labelNumber].getBBox();
      posCheck[i][2] = getQuadrant(newBBox.x2,newBBox.y);
      posCheck[i][3] = newBBox;
      labelsRight--;//decrement the count of right labels
      labelsLeft++;//increment the count of left labels
      i--;//iterate through array, IN REVERSE
    }
  }

  for (i=0; i<labelObjects.length; i++) {
    var labelNumber = posCheck[i][1];
    console.log("Processing label number " + (labelNumber + 1));
    var thisLabel = labelObjects[labelNumber];
    adjustGraphCollision(thisLabel,posCheck[i][2],xCenter,yCenter,pieRadius);
    adjustEdgeCollision(thisLabel);
    changeAnchor(thisLabel);
  }
  var groupBbox = setOfPaths.getBBox();//get a bounding box of all the arcs

  //if all the above succeeded, set the height of the container just before animating the paths
  $('#node-<?php print $nid; ?> .graph-canvas').css('height',canvasHeight + "px");
   
  timedLoop(paths); //call the timedLoop function for fadeIn.

    function label(labelObject,bBox) {
      //object to hold label and associated bbox
    }
    function labelList() {
      //list of labels
    }
    function pie(valuesArray,radius,centerPosArray) {

    }
    function absTranslate(raphaelObject,xTrans,yTrans) {
      var currentX = raphaelObject.attrs.x;
      var currentY = raphaelObject.attrs.y;
      raphaelObject.attr({'x': currentX+xTrans, 'y' : currentY+yTrans});
    }
    //function to iterate over all paths and fade them in one at a time
    var loopCount=0;
    function timedLoop(paths) {
        setTimeout(function () {
          fadeIn(paths[loopCount],300,"0.75");
          loopCount++;
          if (loopCount<paths.length) {
            timedLoop(paths);     
          }
        }, 300);
    }
    //function to sort the labels by distance from middle of graph
    function compareLabelPos(label1,label2) {
      if (label1[0] < label2[0]) {
        return -1;
      } else if (label1[0] > label2[0]) {
        return 1;
      } else {
        return 0;
      }
    }
    function getQuadrant(xPos,yPos) {
      
      //QUADRANT MAP
      
      //  ~ ————— ~
      //  | 4 | 1 |
      //  | ————— |
      //  | 3 | 2 |
      //  ~ ————— ~

      var labelQuadrant = -1; //default case to indicate error
      if (xPos < xCenter)  {
        //label is left of center
        if (yPos < yCenter) {
          labelQuadrant = 4;
        } else {
          labelQuadrant = 3;
        }
      } else {
        //label is right of center
        if (yPos < yCenter) {
          labelQuadrant = 1;
        } else {
          labelQuadrant = 2;
        }
      }
      return labelQuadrant;
    }
    function adjustEdgeCollision(thisLabel) {
      //get a bounding box for checking if the label overflows the available space
      var bbox = thisLabel.getBBox();
      var labelTX = bbox.x;
      var labelTY = bbox.y;
      var labelBX = bbox.x2;
      var labelBY = bbox.y2;

      //check whether label needs to be moved along y or x axis
      //we'll assume the label isn't big enough to need to be 
      //moved in both directions along an axis
      
      //y axis
      if (labelTY < 0) {
        console.log("MOVE DOWN");
        var moveAmt = 0 - labelTY;

        absTranslate(thisLabel,0,moveAmt);
        // thisLabel.transform("t0," + moveAmt);
      } else if (labelBY > canvasHeight) { 
        console.log("MOVE UP");
        var moveAmt = canvasHeight - labelBY;

        absTranslate(thisLabel,0,moveAmt);
        //thisLabel.transform("t0," + moveAmt);
      }

      //x axis
      if (labelTX < 0) {
        console.log("MOVE RIGHT");
        var moveAmt = 0 - labelTX;

        absTranslate(thisLabel,moveAmt,0);
        // thisLabel.transform("t" + moveAmt + ",0");
      } else if (labelBX > canvasWidth) {
        console.log("MOVE LEFT");
        var moveAmt = canvasWidth - labelBX;

        absTranslate(thisLabel,moveAmt,0);
        // thisLabel.transform("t" + moveAmt + ",0");
      }
    }
    function adjustGraphCollision(labelObject,quadrant,centerX,centerY,pieRadius) {
      currentBbox = labelObject.getBBox();
      var xAnchor = -1;
      var yAnchor = -1;
      if (quadrant == 1) {
        xAnchor = currentBbox.x;
        yAnchor = currentBbox.y2;
      } else if (quadrant == 2) { 
        xAnchor =  currentBbox.x;
        yAnchor = currentBbox.y;
      } else if (quadrant == 3) {
        xAnchor = currentBbox.x2;
        yAnchor = currentBbox.y;
      } else { //should be in fourth quadrant, we hope
        xAnchor = currentBbox.x2;
        yAnchor = currentBbox.y2;
      }

      //draw a circle to represent the pie area
      var collisionObj = paper.circle(centerX,centerY,pieRadius).attr({'opacity': 0});
     
      if (collisionObj.isPointInside(xAnchor,yAnchor)) {
        console.log("collision detected, index " + i);
        console.log("quadrant: " + quadrant);
        var angleDeg = Raphael.angle(xAnchor,yAnchor,centerX,centerY);
        var angleRad = Raphael.rad(angleDeg);
        var newPosition = getPointOnCircle(angleRad,pieRadius+10,centerX,centerY);
        var toTransformX = newPosition[0] - xAnchor;
        var toTransformY = newPosition[1] - yAnchor;


        absTranslate(labelObject,toTransformX,toTransformY);
      } //don't do anything else
      
      collisionObj.remove(); //remove the object
    }
    function getPointOnCircle(angle,radius,centerX,centerY) {
      var xCoord = centerX + radius * Math.cos(angle);
      var yCoord = centerY + radius * Math.sin(angle);
      results = [xCoord,yCoord];

      return results;
    }
    function drawArc(centerX,centerY,radius,rotation,percent,isLarge,used,specialFlag,percentThick) {//angle passed in radians, please
     
      //STRINGS FOR TOTAL ARC AND ARC TO ANIMATE FROM
      var arcString = "";
      var arcStart = "";
      var labelLine = "";
      var totalAdj = -(.5*Math.PI); //this will be used in the negative direction
      var rotationRad = (rotation/360) * 2 * Math.PI;
      var adjustment = totalAdj; // subtract 1/2 PI to get 12 o clock position
      totalAdj += rotationRad;//set total adjustment based on rotation parameter

      //SOME ANGLE CALCULATIONS
      var angle = (2 * Math.PI * (percent + used)) + totalAdj //end location
      var halfAngle = (2 * Math.PI * ((percent/2) + used)) + totalAdj; //middle location, for label
      var startAngle = (2 * Math.PI * used) + totalAdj; //start location

      var startX = centerX + radius * Math.cos(startAngle);//calculate outside start
      var startY = centerY + radius * Math.sin(startAngle);
      
      //standard values to compare against
      var rad36 = (2*Math.PI) + adjustment; //270
      var rad27 = (1.5*Math.PI) + adjustment; //180
      var rad18 = Math.PI + adjustment; //90
      var rad9 = (.5*Math.PI) + adjustment; //0
      var rad0 = 0 + adjustment; //-90


      //ADJUSTMENTS FOR LABEL
      //correct values larger than 2 PI
      if (halfAngle > 1.5*Math.PI) { //adjust this an extra quarter to account for svg graph space
        halfAngle = halfAngle - (2 * Math.PI);
      } 

      //Math.PI/6 == 30
      //Math.PI/3 == 60

      //check cases for moving labels away from bottom of graph
      if ( ( rad0 < halfAngle && halfAngle < -Math.PI/6 ) || (rad18 < halfAngle && halfAngle < (rad27 - Math.PI/3)) ) { //this will cause error because rad9 is equal to zero
        halfAngle = halfAngle + (percent * Math.PI * 0.5);//add a quarter percent to the location
      } else if (( (rad27 + Math.PI/3) < halfAngle && halfAngle < rad36) || ((rad9 + Math.PI/3)< halfAngle && halfAngle < rad18) ) {
        halfAngle = halfAngle - (percent * Math.PI * 0.5);
      }
      
      //calculate endpoint :)
      var endX = centerX + radius * Math.cos(angle);//calculating endX by angle so far alone
      var endY = centerY + radius * Math.sin(angle);//same problem as
      
      //caculate the label location, with some distance from the graph
      var labelX = centerX + (radius+1) * Math.cos(halfAngle);
      var labelY = centerY + (radius+1) * Math.sin(halfAngle);
      
      var quadrant = getQuadrant(labelX,labelY);
      var thickness = Math.floor((percentThick/100) * pieRadius);
      var results = new Array();

      //MAKE THE STRING, WITH SWITCH FOR EMPTY MIDDLE
      if (specialFlag != 0) { //pie with hole in the middle
        var smallRadius = radius-thickness;
        var smallStartX = centerX + smallRadius * Math.cos(startAngle);
        var smallStartY = centerY + smallRadius * Math.sin(startAngle);
        var smallEndX = centerX + smallRadius * Math.cos(angle);
        var smallEndY = centerY + smallRadius * Math.sin(angle);

        arcString = "M" + smallStartX + "," + smallStartY;
        arcString += " ";

        arcString += "L" + startX + "," + startY;
        arcString += " ";

        arcStart = arcString; //these are about to diverge
        arcStart += "L" + smallStartX + "," + smallStartY + "z";

        arcString += "A" + radius + "," + radius;
        arcString += ",0," + isLarge + ",1 ";
        arcString += endX + "," + endY;

        arcString += "L" + smallEndX + "," + smallEndY;
        arcString += " ";

        arcString += "A" + smallRadius + "," + smallRadius;
        arcString += ",0," + isLarge + ",0 ";

        arcString += smallStartX + "," + smallStartY;
        arcString += ",z";
      } else {
        //normal code
      arcString = "M" + centerX + "," + centerY;
      arcString += " ";//add a space
        
      arcString += "L" + startX + "," + startY; //initial line
      arcString += " ";//add a space

      arcString += "A" + radius + "," + radius;//we only draw circular arcs, here.
      arcString += ",20," + isLarge + ",1 ";//some required flags and spaces

      arcString += endX + "," +endY;//add the end points
      arcString += ",z";//close the path
      }

      results[0] = arcString;
      results[1] = endX;
      results[2] = endY;
      results[3] = arcStart;
      results[4] = labelX;
      results[5] = labelY;
      results[6] = quadrant;

      return results; //returns an array with the arcString and the end coordinates
    }
    function fadeIn(toAnimate,duration,opacity) {
        toAnimate.animate({"fill-opacity":opacity,"stroke-opacity":"1"},duration, "<>");
    }
    function changeAnchor(labelObject) {
      var currentAnchor = labelObject.attrs['text-anchor'];
      var thisBBox = labelObject.getBBox(); 
      console.log(labelObject);
        if (currentAnchor == "start") {
          labelObject.attr({'text-anchor':'end'});
          absTranslate(labelObject,thisBBox.width,0);
        } else {
          labelObject.attr({'text-anchor':'start'});
          absTranslate(labelObject,-thisBBox.width,0);
        }
    }
  });

</script>
  <?php /*STANDARD NODE RENDER BELOW THIS POINT*/ ?>
  

  <div class="content clearfix"<?php print $content_attributes; ?>>
    <div class="graph">
    <?php
      // We hide the comments and links now so that we can render them later.
      hide($content['comments']);
      hide($content['links']);
      print render($content);
    ?>
    </div>
  </div>
  <?php
    // Remove the "Add new comment" link on the teaser page or if the comment
    // form is being displayed on the same page.
    if ($teaser || !empty($content['comments']['comment_form'])) {
      unset($content['links']['comment']['#links']['comment-add']);
    }
    // Only display the wrapper div if there are links.
    $links = render($content['links']);
    if ($links):
  ?>
    <div class="link-wrapper">
      <?php print $links; ?>
    </div>
  <?php endif; ?>

  <?php print render($content['comments']); ?>

</div>
