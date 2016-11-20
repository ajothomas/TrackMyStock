<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">

	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

	<!-- Include Bootstrap Datepicker -->
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />

	<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>
	<script src="lib/d3.js"></script>
	<script type="text/javascript">
		
		function lineChart(data, companyName) {

		  $("#line_chart").html('');
		  $("#companyName").html(companyName);
		  d3.select("svg").remove();

	      var margin = {top: 20, right: 55, bottom: 30, left: 40},
	          width  = 1000 - margin.left - margin.right,
	          height = 500  - margin.top  - margin.bottom;

	      var x = d3.scale.ordinal()
	          .rangeRoundBands([0, width], .1);

	      var y = d3.scale.linear()
	          .rangeRound([height, 0]);

	      var xAxis = d3.svg.axis()
	          .scale(x)
	          .orient("bottom");

	      var yAxis = d3.svg.axis()
	          .scale(y)
	          .orient("left");

	      var line = d3.svg.line()
	          .interpolate("cardinal")
	          .x(function (d) { return x(d.label) + x.rangeBand() / 2; })
	          .y(function (d) { return y(d.value); });

	      var color = d3.scale.ordinal()
	          .range(["#001c9c","#101b4d","#475003","#9c8305","#d3c47c"]);

	      var svg = d3.select("#line_chart").append("svg")
	          .attr("width",  width  + margin.left + margin.right)
	          .attr("height", height + margin.top  + margin.bottom)
	        .append("g")
	          .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

	      // var dropdown_sel = document.getElementById("text-select").value;
	      // console.log(dropdown_sel);

	     	
	     	var data = data;
	     	console.log(data);
	        var labelVar = "Date";
	        var varNames = ["High","Low","Close"];
	        color.domain(varNames);
	        console.log(varNames);
	      
	        // var data = data.filter(function (d){
	        //         return d.u_id==dropdown_sel;
	        //     });
	        // console.log(data);
	        var seriesData = varNames.map(function (name) {
	          return {
	            name: name,
	            values: data.map(function (d) {
	              return {name: name, label: d[labelVar], value: +d[name]};
	            })
	          };
	        });        
	        console.log(seriesData);

	        x.domain(data.map(function (d) { return d.Date; }));
	        y.domain([
	          d3.min(seriesData, function (c) { 
	            return d3.min(c.values, function (d) { return d.value; });
	          }),
	          d3.max(seriesData, function (c) { 
	            return d3.max(c.values, function (d) { return d.value; });
	          })
	        ]);

	        svg.append("g")
	            .attr("class", "x axis")
	            .attr("transform", "translate(0," + height + ")")
	            .call(xAxis);

	        svg.append("g")
	            .attr("class", "y axis")
	            .call(yAxis)
	          .append("text")
	            .attr("transform", "rotate(-90)")
	            .attr("y", 6)
	            .attr("dy", ".61em")
	            .style("text-anchor", "end")
	            .style("font-size","12px")
	            .text("Stock Points");

	        var series = svg.selectAll(".series")
	            .data(seriesData)
	          .enter().append("g")
	            .attr("class", "series");

	        series.append("path")
	          .attr("class", "line")
	          .attr("d", function (d) { return line(d.values); })
	          .style("stroke", function (d) { return color(d.name); })
	          .style("stroke-width", "4px")
	          .style("fill", "none")

	        series.selectAll(".point")
	          .data(function (d) { return d.values; })
	          .enter().append("circle")
	           .attr("class", "point")
	           .attr("cx", function (d) { return x(d.label) + x.rangeBand()/2; })
	           .attr("cy", function (d) { return y(d.value); })
	           .attr("r", "5px")
	           .style("fill", function (d) { return color(d.name); })
	           .style("stroke", "grey")
	           .style("stroke-width", "2px")
	           .on("mouseover", function (d) { showPopover.call(this, d); })
	           .on("mouseout",  function (d) { removePopovers(); })

	        var legend = svg.selectAll(".legend")
	            .data(varNames.slice().reverse())
	          .enter().append("g")
	            .attr("class", "legend")
	            .attr("transform", function (d, i) { return "translate(55," + (i * 20+ 250) + ")"; });

	        legend.append("rect")
	            .attr("x", width - 10)
	            .attr("width", 10)
	            .attr("height", 10)
	            .style("fill", color)
	            .style("stroke", "grey");

	        legend.append("text")
	            .attr("x", width - 12)
	            .attr("y", 6)
	            .attr("dy", ".35em")
	            .style("text-anchor", "end")
	            .text(function (d) { return d; });

	        function removePopovers () {
	          $('.popover').each(function() {
	            $(this).remove();
	          }); 
	        }

	        function showPopover (d) {
	          $(this).popover({
	            title: d.name,
	            placement: 'auto top',
	            container: 'body',
	            trigger: 'manual',
	            html : true,
	            content: function() { 
	              return "Week: " + d.label + 
	                     "<br/>Count: " + d3.format(",")(d.value ? d.value: d.y1 - d.y0); }
	          });
	          $(this).popover('show')
	        }
  	}


	</script>
</head>
<body>
<nav class="navbar navbar-default" align="center">
  <div class="container-fluid">
    <div class="navbar-header" >
      <img src='TrackMyStock.png' style="width:150px;height:50px;"/>
    </div>
  </div>
</nav>
	
<div class="container" align="top">
	<form method="POST" action="index.php">

	<div class="row">
		<div class="col-sm-8">
			<div class="form-group">
				<label for="email">Enter Stock Symbol</label>
	      		<input type="text" class="form-control" name="stockSymbol" required>
	      	</div>
	    </div>
	</div>
	
	<div class="row">
		<div class="col-sm-4">
			<div class="form-group">
				<label for="email">Start Date</label>
				<div class="input-group input-append date" id="datepicker1">
	                <input type="text" class="form-control" name="dp1" required />
	                <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
            	</div>
            </div>
		</div> 

		<div class="col-sm-4">
			<div class="form-group">
				<label for="email">End Date</label>
				<div class="input-group input-append date" id="datepicker2">
	                <input type="text" class="form-control" name="dp2" required/>
	                <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
            	</div>
            </div>
		</div>            
	</div>

	<div class="row">
		   
	</div>
	<div class="row">
		<div class="col-sm-4">
			<button type="submit" class="btn btn-default">Submit</button>
		</div>        
	</div>
	</form>	
	<hr/>
	
	<h3><span class="label label-default" id="companyName">Stock Trend: </span></h3>
	<div id="line_chart">
		No chart to show
	</div>
	
</div>

	



<?php

if(isset($_POST['stockSymbol'])){ //check if form was submitted
	
	$stockSymbol = $_POST['stockSymbol']; //get input text
	$startDate = $_POST['dp1'];
	$endDate = $_POST['dp2'];
	$url1 = "http://query.yahooapis.com/v1/public/yql?q=select%20Date,High,Low,Close%20from%20yahoo.finance.historicaldata%20where%20symbol%20=%20%27".$stockSymbol."%27%20and%20startDate%20=%20%27".$startDate."%27%20and%20endDate%20=%20%27".$endDate."%27&format=json&env=store://datatables.org/alltableswithkeys"; 

	$json_str1 = file_get_contents($url1);
	$json_obj1 = json_decode( $json_str1, true );
	$json_arr1 = $json_obj1["query"]["results"]["quote"];


	$url2 = "http://query.yahooapis.com/v1/public/yql?q=select%20Symbol,Name%20from%20yahoo.finance.quotes%20where%20symbol%20IN%20(%22".$stockSymbol."%22)&format=json&env=http://datatables.org/alltables.env"; 

	$json_str2 = file_get_contents($url2);
	$json_obj2 = json_decode( $json_str2, true );
	$json_arr2 = $json_obj2["query"]["results"]["quote"]["Name"];
	if(is_null($json_arr2)){
		?>
			<script type='text/javascript'>
			$('#line_chart').html('Company records not found');
			$('#companyName').html('');
			</script>
		<?php
	}
	else if(is_null($json_arr1)){
		?>
			<script type="text/javascript">	
				$('#line_chart').html('No chart to show. Check dates');
				$('#companyName').html(<?php echo json_encode($json_arr2); ?>);
			</script>
		<?php
	}
	else{
		?>
			<script type="text/javascript">	
				var arr1 = <?php echo json_encode($json_arr1); ?>;
				var companyName = <?php echo json_encode($json_arr2); ?>;
				lineChart(arr1,companyName);
			</script>
		<?php
	}
}
else{
?>
<script>
	$('#line_chart').html('');
	$('#companyName').html('');
</script>

<?php
}

// else ends
?>
</body>

<script>

$(function() {

	$('#datepicker1').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd'
		})
		.on('changeDate', function(e) {
			// Revalidate the date field
			console.log("hi");
	});

	$('#datepicker2').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd'
		})
			.on('changeDate', function(e) {
			// Revalidate the date field
			console.log("hi");
	});
});

</script>


</html>