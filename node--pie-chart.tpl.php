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
  
    .graph-spot {
      height:400px;
      box-sizing: border-box;
      position: relative;
      z-index: 2;
      border-top: 1px solid #d7d7d7;
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
  </style>
  <?php 
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
  ?>
  <script type="text/javascript">
  //we don't want any global variables in case there are multiple graphs, or else all the graph values will be the same.
    
    var loopCount<?php print $nid;?>=0;
    function timedLoop (paths) {
        setTimeout(function () {
          fadeIn(paths[loopCount<?php print $nid;?>],300,"0.75");
          loopCount<?php print $nid;?>++;
          if (loopCount<?php print $nid;?><paths.length) {
            timedLoop(paths);     
          }
        }, 300);
    }
    function drawArc(centerX,centerY,radius,startX,startY,percent,isLarge,used,specialFlag) {//angle passed in radians, pleased
      var arcString = "M" + centerX + "," + centerY;
      arcString += " ";//add a space

      var angle = 2 * Math.PI * (percent + used);
      var startAngle = 2 * Math.PI * used;
      //console.log(angle);
      //calculate endpoint :)
      var endX = centerX + radius * Math.cos(angle);//calculating endX by angle so far alone
      var endY = centerY + radius * Math.sin(angle);//same problem as

      var thickness = 48;
      var results = new Array();
      if (specialFlag != 0) { //pie with hole in the middle
        var smallRadius = radius-thickness;
        var smallStartX = centerX + smallRadius * Math.cos(startAngle);
        var smallStartY = centerY + smallRadius * Math.sin(startAngle);
        var smallEndX = centerX + smallRadius * Math.cos(angle);
        var smallEndY = centerY + smallRadius * Math.sin(angle);

        arcString += "M" + smallStartX + "," + smallStartY;
        arcString += " ";

        arcString += "L" + startX + "," + startY;
        arcString += " ";

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

      return results; //returns an array with the arcString and the end coordinates
    }

    function fadeIn(toAnimate,duration,opacity) {
      toAnimate.animate({"fill-opacity":opacity,"stroke-opacity":"1"},duration, "<>");
    }

    $(document).ready(function(){
      /*OTHER STUFF*/
        var GraphTitle = <?php print drupal_json_encode($title); ?>;
        var fieldCollections = <?php print drupal_json_encode($fc_entities); ?>; 
        var fc_ids = <?php print drupal_json_encode($ids); ?>;
        //console.log(thickness);
        var $gTitle = $("<h3 class='gtitle'></h3>").html(GraphTitle);
        <?php $nid = $node->nid; ?>
        $('#node-<?php print $nid; ?> .graph').append('<div class="graph-spot"></div>').append('<div class="graph-canvas"></div>').append('<div class="graph-labels"></div>').append('<div class="x-axis-label"></div>');
        var canvasHeight = $('#node-<?php print $nid; ?> .graph-spot').height();
        var canvasWidth =  $('#node-<?php print $nid; ?> .graph-spot').width();
        var canvasPosition = $('#node-<?php print $nid; ?> .graph-spot').position();
        var graphRadius = -1;
        if (canvasHeight < canvasWidth) {
          graphRadius = Math.floor(canvasHeight/2);
        } else {
          graphRadius = Math.floor(canvasWidth/2);
        }
        //console.log(fc_ids);
        //console.log(graphRadius);
        //console.log(canvasPosition);
        pieRadius = graphRadius - 20;
        //console.log(startPoint);
        //console.log(canvasHeight);
        $gTitle.insertBefore($('#node-<?php print $nid; ?> .graph'));
        paper<?php print $nid;?> = Raphael($("#node-<?php print $nid; ?> .graph-spot")[0], canvasWidth, canvasHeight);
        
        //var currentArc = drawArc(graphRadius, graphRadius,pieRadius,(graphRadius+pieRadius),graphRadius,.4,1);
        //console.log(currentArc);
        //var $newArc = paper.path(currentArc[0]);
        //$newArc.attr({fill:'#d7d7d7'});
        //iterate over the data in field collections
        var values = [];
        var total = parseInt(0);
        for (i=0; i<fieldCollections.length; i++) {
          var rawHeight = fieldCollections[i][fc_ids[i]].field_value.und[0].value;
          var sgLabel = fieldCollections[i][fc_ids[i]].field_label.und[0].safe_value;
          //console.log(rawHeight);
          //console.log(sgLabel);
          values.push(rawHeight);
          total += parseInt(rawHeight);
          var $graphLabel = $("<div class='glabel'></div>");
          //$graphLabel.html(sgLabel).attr('style','width:' + percentWidth + "%;");
          //console.log(graphLabel);
          $('#node-<?php print $nid; ?> .graph-labels').append($graphLabel);
          
        }

      //console.log(values);
      //console.log(total);
      var percentages = [];
      for (i=0; i<values.length; i++) {
        percentages.push(values[i]/total);
      }
     // console.log(percentages);
      var paths<?php print $nid; ?> = [];
      var xStart = (graphRadius+pieRadius)
      var yStart = graphRadius;
      var isUsed = 0;
      for (i=0; i<percentages.length; i++) {
        var largeArc = 0;
        if (percentages[i]>0.5) {
          largeArc = 1;
        } //no else
        var currentArc = drawArc(graphRadius,graphRadius,pieRadius,xStart,yStart,percentages[i],largeArc,isUsed,1);
        var currentColor = fieldCollections[i][fc_ids[i]].field_color.und[0].rgb;
        //console.log(currentArc);
         
        var raphaelObject = paper<?php print $nid;?>.path(currentArc[0]).attr({
          fill: currentColor,
          stroke: "white",
          "stroke-width": 2
        }).mouseover(function () {
                this.stop().animate({"fill-opacity":" 1"}, 200, "<>");
                // txt.stop().animate({opacity: 1}, ms, "elastic");
            }).mouseout(function () {
                this.stop().animate({"fill-opacity": "0.75"}, 200, "<>");
                // txt.stop().animate({opacity: 0}, ms);
            });

       
        paths<?php print $nid; ?>.push(raphaelObject);
        isUsed += percentages[i];//update how much has already been consumed
       // console.log(currentArc[1]);
       // console.log(currentArc[2]);
        xStart = currentArc[1];
        yStart = currentArc[2];
      }
     
      // var circle = "circle cx=" + graphRadius + " cy=" + graphRadius + " r=" + (pieRadius-30) + " stroke=black";
      //   paths.push(circle);
      timedLoop(paths<?php print $nid; ?>);

  
      $('#node-<?php print $nid; ?> .graph-canvas').css('height',canvasHeight + "px");


    });
  </script>
  <?php print render($title_prefix); ?>
  <?php if (!$page): ?>
    <h2<?php print $title_attributes; ?>>
      <a href="<?php print $node_url; ?>"><?php print $title; ?></a>
    </h2>
  <?php endif; ?>
  <?php print render($title_suffix); ?>

  <?php if ($display_submitted): ?>
    <div class="meta submitted">
      <?php print $user_picture; ?>
      <?php print $submitted; ?>
    </div>
  <?php endif; ?>

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
