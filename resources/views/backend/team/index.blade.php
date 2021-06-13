@extends('layouts.backend',['page'=>'team','active'=>'sponsor'])

@section('header')
  <h4 class="font-color-purple"><i class="fa fa-sitemap"></i> <span>Sponsor Tree</span></h4>
@endsection

@section('content')
  <div class="col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="ibox-home bg-boxshadow">
      @include('layouts.partials.alert')
      <div class="ibox-title mb-20">
        <div class="row">
          <div class="col-md-8"></div>
          <div class="col-md-4">
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-append" onclick="refresh();">
                        <button type="submit" class="btn btn-danger">Reset</button>
                    </span>
                    <input id="username" type='text' class="form-control" placeholder="Search username"/>
                    <span class="input-group-append" id="btn_search">
                        <button type="submit" class="btn btn-warning"><i class="fa fa-search"></i></button>
                    </span>
                </div>
                <p id="text-username" class="text-helper text-danger"></p>
            </div>
          </div>
        </div>
      </div>

      <div class="ibox-content">
          <div id="myDiagramDiv" style="background-color: transparent; border: unset; height: 500px"></div>
      </div>
    </div>
  </div>
@endsection

@section('script')
<script type="text/javascript">
    var id_user = {{Auth::user()->id}},
        component = [],
        child = [],
        page = 0;
    var myDiagram;
  	$(window).load(function() {
    	init();
  	});

    function init() {
        if (window.goSamples) goSamples();  // init for these samples -- you don't need to call this
        var $ = go.GraphObject.make;  // for conciseness in defining templates

        myDiagram =
          $(go.Diagram, "myDiagramDiv", // must be the ID or reference to div
            {
              initialContentAlignment: go.Spot.Center,
              initialAutoScale: go.Diagram.Uniform,
              maxSelectionCount: 1, // users can select only one part at a time
              validCycle: go.Diagram.CycleDestinationTree, // make sure users can only create trees
              "clickCreatingTool.archetypeNodeData": {}, // allow double-click in background to create a new node
              "clickCreatingTool.insertPart": function(loc) {  // customize the data for the new node
                this.archetypeNodeData = {
                  key: getNextKey(), // assign the key based on the number of nodes
                  name: "(new person)",
                  title: ""
                };
                return go.ClickCreatingTool.prototype.insertPart.call(this, loc);
              },
              layout:
                $(SideTreeLayout,
                  {
                    treeStyle: go.TreeLayout.StyleLastParents,
                    arrangement: go.TreeLayout.ArrangementHorizontal,
                    // properties for most of the tree:
                    angle: 90,
                    layerSpacing: 35,
                    // properties for the "last parents":
                    alternateAngle: 90,
                    alternateLayerSpacing: 35,
                    alternateAlignment: go.TreeLayout.AlignmentBus,
                    alternateNodeSpacing: 20
                  }),
              "undoManager.isEnabled": true // enable undo & redo
            });

        var levelColors = ["#f66718", "#f66718", "#f66718", "#f66718",
                           "#f66718", "#f66718", "#f66718", "#f66718"];

        // override TreeLayout.commitNodes to also modify the background brush based on the tree depth level
        myDiagram.layout.commitNodes = function() {
          go.TreeLayout.prototype.commitNodes.call(myDiagram.layout);  // do the standard behavior
          // then go through all of the vertexes and set their corresponding node's Shape.fill
          // to a brush dependent on the TreeVertex.level value
          myDiagram.layout.network.vertexes.each(function(v) {
            if (v.node) {
              var level = v.level % (levelColors.length);
              var color = levelColors[level];
              var shape = v.node.findObject("SHAPE");
              if (shape) shape.fill = $(go.Brush, "Linear", { 0: color, 1: go.Brush.lightenBy(color, 0.05), start: go.Spot.Left, end: go.Spot.Right });
            }
          });
        };

        // This function is used to find a suitable ID when modifying/creating nodes.
        // We used the counter combined with findNodeDataForKey to ensure uniqueness.
        function getNextKey() {
          var key = nodeIdCounter;
          while (myDiagram.model.findNodeDataForKey(key) !== null) {
            key = nodeIdCounter--;
          }
          return key;
        }

        var nodeIdCounter = -1; // use a sequence to guarantee key uniqueness as we add/remove/modify nodes

        // when a node is double-clicked, add a child to it
        function nodeDoubleClick(e, obj) {
            var clicked = obj.part;
             if (clicked !== null) {
                var id = clicked.data.key;
                var parent = clicked.data.parent;
                var position = clicked.data.position;
                var pageCount = clicked.data.page;
                var name = clicked.data.name;
                if(id > 0){
                  if(id_user != id){
                    var ary = isInArray(parent,component);
                    if(ary == true){
                        load(parent);
                        removeItem(component, parent);
                        child = [];
                    }else{
                        var cld = isInArray(id,child);
                        if(cld != true){
                            load(id);
                        }
                        child.push(id);
                        component.push(parent);
                    }
                  }
                }else{
                    if(name == 'Prev'){
                        page = page - 1;
                    }
                    if(name == 'Next'){
                        page = page + 1;
                    }
                    load(parent);
                }
            }
        }

        function isInArray(value, array) {
          return array.indexOf(value) > -1;
        }

        function removeItem(array, item){
            for(var i in array){
                if(array[i]==item){
                    array.splice(i,1);
                    break;
                }
            }
        }

        // this is used to determine feedback during drags
        function mayWorkFor(node1, node2) {
          if (!(node1 instanceof go.Node)) return false;  // must be a Node
          if (node1 === node2) return false;  // cannot work for yourself
          if (node2.isInTreeOf(node1)) return false;  // cannot work for someone who works for you
          return true;
        }

        // This function provides a common style for most of the TextBlocks.
        // Some of these values may be overridden in a particular TextBlock.
        function textStyle() {
          return { font: "9pt  Segoe UI,sans-serif", stroke: "white" };
        }

        // This converter is used by the Picture.
        function findHeadShot() {
          // if (key < 0 || key > 16) return "images/HSnopic.png"; // There are only 16 images on the server
          // return "images/HS" + key + ".png"
          var img = '{{asset('images/logo/usr.png')}}';
          return img;
        }

        // define the Node template
        myDiagram.nodeTemplate =
          $(go.Node, "Auto",
            // { doubleClick: nodeDoubleClick },
            { click: nodeDoubleClick },
            { // handle dragging a Node onto a Node to (maybe) change the reporting relationship
              mouseDragEnter: function (e, node, prev) {
                var diagram = node.diagram;
                var selnode = diagram.selection.first();
                if (!mayWorkFor(selnode, node)) return;
                var shape = node.findObject("SHAPE");
                if (shape) {
                  shape._prevFill = shape.fill;  // remember the original brush
                  shape.fill = "darkred";
                }
              },
              mouseDragLeave: function (e, node, next) {
                var shape = node.findObject("SHAPE");
                if (shape && shape._prevFill) {
                  shape.fill = shape._prevFill;  // restore the original brush
                }
              },
              mouseDrop: function (e, node) {
                var diagram = node.diagram;
                var selnode = diagram.selection.first();  // assume just one Node in selection
                if (mayWorkFor(selnode, node)) {
                  // find any existing link into the selected node
                  var link = selnode.findTreeParentLink();
                  if (link !== null) {  // reconnect any existing link
                    link.fromNode = node;
                  } else {  // else create a new link
                    diagram.toolManager.linkingTool.insertLink(node, node.port, selnode, selnode.port);
                  }
                }
              }
            },
            // for sorting, have the Node.text be the data.name
            new go.Binding("text", "name"),
            // bind the Part.layerName to control the Node's layer depending on whether it isSelected
            new go.Binding("layerName", "isSelected", function(sel) { return sel ? "Foreground" : ""; }).ofObject(),
            // define the node's outer shape
            $(go.Shape, "RoundedRectangle",
              {
                name: "SHAPE", fill: "white", stroke: null,
                portId: "", fromLinkable: true, toLinkable: true, cursor: "pointer"
              }),
            $(go.Panel, "Horizontal",
              $(go.Panel, "Table",
                {
                  maxSize: new go.Size(150, 999),
                  margin: new go.Margin(5, 10, 5, 10),
                  defaultAlignment: go.Spot.Center
                },
                $(go.Panel, "Spot",
                  { isClipping: true , margin: new go.Margin(2, 55, 2, 55)},
                  $(go.Shape, "Circle", { row: 0, column: 0, columnSpan: 5, width: 45, strokeWidth: 0 } ),
                  $(go.Picture, findHeadShot(),
                    { row: 0, column: 0, columnSpan: 5, width: 45, height: 45 }
                   )
                ),
                $(go.TextBlock, textStyle(),  // the name
                  {
                    row: 1, column: 0, columnSpan: 5,
                    font: "12pt Segoe UI,sans-serif",
                    editable: false, isMultiline: false,
                    minSize: new go.Size(10, 14)
                  },
                  new go.Binding("text", "username").makeTwoWay()),
                $(go.TextBlock, textStyle(),
                  { row: 2, column: 0, columnSpan: 5 },
                  new go.Binding("text", "", function(v) {
                      if(v.name != 'Prev' && v.name != 'Next'){
                        return "Package: " + v.program + " Downline: " + v.downline;
                      }
                }))
              )  // end Table Panel
            ) // end Horizontal Panel
          );  // end Node

        // define the Link template
        myDiagram.linkTemplate =
          $(go.Link, go.Link.Orthogonal,
            { corner: 5, relinkableFrom: true, relinkableTo: true },
            $(go.Shape, { strokeWidth: 4, stroke: "#33414e" }));  // the link shape

        // read in the JSON-format data from the "mySavedModel" element
        load(id_user);
    }


    // Assume that the SideTreeLayout determines whether a node is an "assistant" if a particular data property is true.
    // You can adapt this code to decide according to your app's needs.
    function isAssistant(n) {
        if (n === null) return false;
        return n.data.isAssistant;
    }


    // This is a custom TreeLayout that knows about "assistants".
    // A Node for which isAssistant(n) is true will be placed at the side below the parent node
    // but above all of the other child nodes.
    // An assistant node may be the root of its own subtree.
    // An assistant node may have its own assistant nodes.
    function SideTreeLayout() {
        go.TreeLayout.call(this);
    }
    go.Diagram.inherit(SideTreeLayout, go.TreeLayout);

    SideTreeLayout.prototype.makeNetwork = function(coll) {
        var net = go.TreeLayout.prototype.makeNetwork.call(this, coll);
        // copy the collection of TreeVertexes, because we will modify the network
        var vertexcoll = new go.Set(go.TreeVertex);
        vertexcoll.addAll(net.vertexes);
        for (var it = vertexcoll.iterator; it.next() ;) {
          var parent = it.value;
          // count the number of assistants
          var acount = 0;
          var ait = parent.destinationVertexes;
          while (ait.next()) {
            if (isAssistant(ait.value.node)) acount++;
          }
          // if a vertex has some number of children that should be assistants
          if (acount > 0) {
            // remember the assistant edges and the regular child edges
            var asstedges = new go.Set(go.TreeEdge);
            var childedges = new go.Set(go.TreeEdge);
            var eit = parent.destinationEdges;
            while (eit.next()) {
              var e = eit.value;
              if (isAssistant(e.toVertex.node)) {
                asstedges.add(e);
              } else {
                childedges.add(e);
              }
            }
            // first remove all edges from PARENT
            eit = asstedges.iterator;
            while (eit.next()) { parent.deleteDestinationEdge(eit.value); }
            eit = childedges.iterator;
            while (eit.next()) { parent.deleteDestinationEdge(eit.value); }
            // if the number of assistants is odd, add a dummy assistant, to make the count even
            if (acount % 2 == 1) {
              var dummy = net.createVertex();
              net.addVertex(dummy);
              net.linkVertexes(parent, dummy, asstedges.first().link);
            }
            // now PARENT should get all of the assistant children
            eit = asstedges.iterator;
            while (eit.next()) {
              parent.addDestinationEdge(eit.value);
            }
            // create substitute vertex to be new parent of all regular children
            var subst = net.createVertex();
            net.addVertex(subst);
            // reparent regular children to the new substitute vertex
            eit = childedges.iterator;
            while (eit.next()) {
              var ce = eit.value;
              ce.fromVertex = subst;
              subst.addDestinationEdge(ce);
            }
            // finally can add substitute vertex as the final odd child,
            // to be positioned at the end of the PARENT's immediate subtree.
            var newedge = net.linkVertexes(parent, subst, null);
          }
        }
        return net;
    };

    SideTreeLayout.prototype.assignTreeVertexValues = function(v) {
        // if a vertex has any assistants, use Bus alignment
        var any = false;
        var children = v.children;
        for (var i = 0; i < children.length; i++) {
          var c = children[i];
          if (isAssistant(c.node)) {
            any = true;
            break;
          }
        }
        if (any) {
          // this is the parent for the assistant(s)
          v.alignment = go.TreeLayout.AlignmentBus;  // this is required
          v.nodeSpacing = 50; // control the distance of the assistants from the parent's main links
        } else if (v.node == null && v.childrenCount > 0) {
          // found the substitute parent for non-assistant children
          //v.alignment = go.TreeLayout.AlignmentCenterChildren;
          //v.breadthLimit = 3000;
          v.layerSpacing = 0;
        }
    };

    SideTreeLayout.prototype.commitLinks = function() {
        go.TreeLayout.prototype.commitLinks.call(this);
        // make sure the middle segment of an orthogonal link does not cross over the assistant subtree
        var eit = this.network.edges.iterator;
        while (eit.next()) {
          var e = eit.value;
          if (e.link == null) continue;
          var r = e.link;
          // does this edge come from a substitute parent vertex?
          var subst = e.fromVertex;
          if (subst.node == null && r.routing == go.Link.Orthogonal) {
            r.updateRoute();
            r.startRoute();
            // middle segment goes from point 2 to point 3
            var p1 = subst.center;  // assume artificial vertex has zero size
            var p2 = r.getPoint(2).copy();
            var p3 = r.getPoint(3).copy();
            var p5 = r.getPoint(r.pointsCount - 1);
            var dist = 10;
            if (subst.angle == 270 || subst.angle == 180) dist = -20;
            if (subst.angle == 90 || subst.angle == 270) {
              p2.y = p5.y - dist; // (p1.y+p5.y)/2;
              p3.y = p5.y - dist; // (p1.y+p5.y)/2;
            } else {
              p2.x = p5.x - dist; // (p1.x+p5.x)/2;
              p3.x = p5.x - dist; // (p1.x+p5.x)/2;
            }
            r.setPoint(2, p2);
            r.setPoint(3, p3);
            r.commitRoute();
          }
        }
    };  // end of SideTreeLayout

    function load(id) {
        var url = '{{URL::to('team/getSponsorTree')}}/'+id+'/'+page;
        $.ajax({
            url: url,
            type: "GET",
            contentType: "application/json",
            success: function (data) {
                myDiagram.model = go.Model.fromJson(data);
            },
            cache: false
        });
    }

    function refresh() {
        page = 0;
        load(id_user,page);
        child = [];
        component = [];
    };


    $('#btn_search').on('click', function () {
      var username = $('#username').val();
      $.ajax({
          url: '{{route('user.searchUser')}}',
          data:{
            username : username
          },
          type: "GET",
          contentType: "application/json",
          success: function (data) {
              if(data.error == false){
                page = 0;
                load(data.data.id);
              }
          },
          cache: false
      });
    });

    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
</script>
@endsection
