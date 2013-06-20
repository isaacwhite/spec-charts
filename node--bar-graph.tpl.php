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

  <?php
  //dpm($node);
  //$field_collection_item = field_collection_item_load(0,FALSE);
  //print $field_collection_item;
  //dpm($field_collection_item);

  //$fc = entity_load('field_collection_item', array(1));
  //dpm($fc);
  // dpm($node->field_value_and_label->und.length);
  // Extract the field collection item ids
  
  //Load the field ids
  $fc_fields = field_get_items('node', $node, 'field_value_and_label');
  $ids = array();
  foreach ($fc_fields as $fc_field) {
     $ids[] = $fc_field['value'];
  } //put the ids into an array
  
   /*//dpm($ids);
   //make a new array of the actual field colleciton entities
   $fc_entities = array();
   //loop through the array loading each field collection
   // foreach ($ids as $fc_id) {
   //  $fc_entities[]= entity_load('field_collection_item', array($fc_id['FieldCollectionItemEntity']));
   // }
   for ( $i=0; $i< count($ids); $i++) {
      dpm($ids[$i]);
      $fc_entities[]= entity_load('field_collection_item', array($ids[$i]));
   };
   dpm($fc_entities);
  //$fc = entity_load($)
  
  //$field_collection_item = field_collection_item_load(1);
  //dpm($field_collection_item);
  */?>
  <style>
  .outer-bar {
    margin: .5%;
    /*background-color: red;*/
    display:inline-block;
    /*opacity: 0;*/
    /*cursor: pointer;*/
    /*transition: all ease slow;*/
    position:relative;
  }
  .inner-bar {
    height: 0%;
    width: 100%;
    background-color: red;
    cursor: pointer;
    position:absolute;
    bottom:0;
  }
  .inner-bar.loaded {
    /*transition: all .1s linear;*/
  }
  .inner-bar:hover {
    background-color: yellow;
  }
  #graph-spot {
    height:400px;
  }
  #graph-labels {
    width:100%;
    height:50px;
  }
  .glabel {
    display: inline-block;
    text-align: center;
    margin: .5%;
  }
  #x-axis-label {
    display: block;
    text-align: center;
    text-transform: uppercase;
  }
  .gtitle {
    padding: .5em;
    text-transform: uppercase;
    background-color: black;
    color: white;
    font-family: helvetica, sans-serif;
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
   
       //loop through the array loading each field collection
       // foreach ($ids as $fc_id) {
       //  $fc_entities[]= entity_load('field_collection_item', array($fc_id['FieldCollectionItemEntity']));
       // }
       for ( $i=0; $i< count($ids); $i++) {
          //dpm($ids[$i]);
          $fc_entities[]= entity_load('field_collection_item', array($ids[$i]));
       };
       //dpm($fc_entities);
  ?>
  <script src="/themes/bartik/js/jquery-1.9.1.min.js"></script>
  <script type="text/javascript">
      var GraphTitle = <?php print drupal_json_encode($title); ?>;
      var VertVal = <?php print drupal_json_encode($node->field_vertical_value); ?>;
      var VertLabels = <?php print drupal_json_encode($node->field_labels_for_vertical_values); ?>;
      var VertUnit = <?php print drupal_json_encode($node->field_vertical_unit); ?>;
      var HorzUnit = <?php print drupal_Json_encode($node->field_horizontal_unit); ?>;
      var lineCount = <?php print drupal_json_encode($node->field_number_of_horizontal_lines); ?>;
      var VertMax = <?php print drupal_json_encode($node->field_maximum_value); ?>;
      var percentWidth = (100 - VertVal.und.length)/VertVal.und.length; 
      var fieldCollections = <?php print drupal_json_encode($fc_entities); ?>; 
    $(document).ready(function(){
      var $gTitle = $("<h3 class='gtitle'></h3>").html(GraphTitle);
      $('#graph').prepend($gTitle).append('<div id="graph-spot"></div>').append('<div id="graph-labels"></div>').append('<div id="x-axis-label"></div>').prepend('');
      
      //var FieldCollection = <?php print drupal_json_encode($node->field_value_and_label); ?>;
     // console.log(gTitle);
     // console.log(gVertVal);
     console.log(VertLabels);
     console.log("Vertical Values");
      for (i=0; i<VertVal.und.length; i++) {
        // for (i=0; )
        //console.log(VertVal.und[i].value);

        var canvasHeight = $('#graph-spot').height();
        var rawHeight = VertVal.und[i].value;
        var maxHeight = VertMax.und[0].value;
        var height = ((rawHeight/maxHeight)*canvasHeight);
        
        //console.log(percentWidth);
        var newBar = $("<div class='outer-bar'>")
          .attr('style', 'height: ' + height.toFixed(0) + 'px;' + ' width:' + percentWidth + "%;" )
          .append(
              $("<div class='inner-bar'>")
            );
        //var inBar = $("<div class='in-bar'>");
       // var wholeBar = $(inBar.wrap(newBar));
          $('#graph-spot').append(newBar);
      }
      for (j=0; j<VertLabels.und.length; j++) {
        var $graphLabel = $("<div class='glabel'></div>");
        $graphLabel.html(VertLabels.und[j].safe_value).attr('style','width:' + percentWidth + "%;");
        //console.log(graphLabel);
        $('#graph-labels').append($graphLabel);
      }
      $('#x-axis-label').html(HorzUnit.und[0].value);
      // $('#graph-labels').html('hello'); 
      
      /*console.log("Vertical Labels");
      for (i=0; i<VertLabels.und.length; i++) {
      console.log(VertLabels.und[i].value);
      }
      console.log(rawHeight/VertMax.und[0].value);
      console.log(parseFloat((VertMax)/parseFloat(rawHeight) ));
      console.log("Vertical Unit");
      console.log(VertUnit.und[0].value);
      console.log("Horizontal Unit");
      console.log(HorzUnit.und[0].value);
      console.log("Horizontal Line Count");
      console.log(lineCount.und[0].value);
      console.log("Maximum Value:");
      console.log(VertMax.und[0].value);
      */
      $('.inner-bar').animate({height:'100%'},500).addClass('loaded');


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
    <div id="graph">
    <?php
      // We hide the comments and links now so that we can render them later.
      hide($content['comments']);
      hide($content['links']);
      print render($content);
    ?>
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
