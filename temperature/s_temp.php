<?php
$hostname = 'localhost';
$username = 'pi_select';
$password = 'select01';

try {
    $dbh = new PDO("mysql:host=$hostname;dbname=measurements", 
                               $username, $password);

    /*** The SQL SELECT statement ***/
    $sth = $dbh->prepare("
       SELECT  `dtg`, `temperature` FROM  `temperature` LIMIT 40000 , 100000
    ");
    $sth->execute();

    /* Fetch all of the remaining rows in the result set */
    $result = $sth->fetchAll(PDO::FETCH_ASSOC);

    /*** close the database connection ***/
    $dbh = null;
    
}
catch(PDOException $e)
    {
        echo $e->getMessage();
    }

$json_data = json_encode($result); 
?>

<!DOCTYPE html>
<meta charset="utf-8">
<style> /* set the CSS */

body { font: 12px Arial;}

path {
    stroke: steelblue;
    stroke-width: 2;
    fill: none;
}

.axis path,
.axis line {
    fill: none;
    stroke: grey;
    stroke-width: 1;
    shape-rendering: crispEdges;
}
.grid .tick {
    stroke: lightgrey;
    stroke-opacity: 0.7;
    shade-rendering: crispEdges;
}
.grid path {
    stroke-width: 0;
}


</style>
<body>

<div>
    <input name="updateButton"
           type="button"
           value="Update"
           onclick="updateData()" 
    />
</div>


<!-- load the d3.js library -->
<script src="http://d3js.org/d3.v3.min.js"></script>

<script>

// Set the dimensions of the canvas / graph
var margin = {top: 40, right: 20, bottom: 30, left: 50},
    width = 1800 - margin.left - margin.right,   //800
    height = 800 - margin.top - margin.bottom;  //270

// Parse the date / time
var parseDate = d3.time.format("%Y-%m-%d %H:%M:%S").parse;

// Set the ranges
var x = d3.time.scale().range([0, width]);
var y = d3.scale.linear().range([height, 0]);

// Define the axes
var xAxis = d3.svg.axis().scale(x)
    .orient("bottom");

var yAxis = d3.svg.axis().scale(y)
    .orient("left").ticks(10);

// Define the line
var valueline = d3.svg.line()
    .interpolate("monotone") //linear, step-before, step-after, basis, basis-closed, 
        //bundle, cardinal, cardinal-open, cardinal-closed, monotone 
    .x(function(d) { return x(d.dtg); })
    .y(function(d) { return y(d.temperature); });

// Adds the svg canvas
var svg = d3.select("body")
    .append("svg")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
    .append("g")
        .attr("transform",
              "translate(" + margin.left + "," + margin.top + ")");

function updateData() {
    // Get the data
    <?php echo "data=".$json_data.";" ?>
        data.forEach(function(d) {
            d.dtg = parseDate(d.dtg);
            d.temperature = +d.temperature;
        });

        // Scale the range of the data
        x.domain(d3.extent(data, function(d) { return d.dtg; }));
        y.domain([0, d3.max(data, function(d) { return d.temperature; })]);
    


    // select the section we want to apply changes to

    var svg = d3.select("body").transition();

    // Make the changes
        svg.select(".line") //change the line
            .duration(750)
            .attr("d", valueline(data));
        svg.select(".x.axis") // change the x axis
            .duration(750)
            .call(xAxis);
        svg.select(".y.axis") // change y axis
            .duration(750)
            .call(yAxis);
}

// Adds 2 functions to generate the grid (x and y)
function make_x_axis() {
    return d3.svg.axis()
        .scale(x)
        .orient("bottom")
        .ticks(30)
}

function make_y_axis() {
    return d3.svg.axis()
        .scale(y)
        .orient("left")
        .ticks(10)
}


// Get the data
<?php echo "data=".$json_data.";" ?>
data.forEach(function(d) {
 d.dtg = parseDate(d.dtg);
 d.temperature = +d.temperature;
});

// Scale the range of the data
x.domain(d3.extent(data, function(d) { return d.dtg; }));
y.domain([0, d3.max(data, function(d) { return d.temperature; })]);

// Add the valueline path (and transition)
svg.append("path")
    .attr("class", "line")
    //.style("stroke-dasharray", ("3, 10"))
    .attr("d", valueline(data))
  .transition()
    .duration(1000000) // time in miliseconds
    .attrTween("d", pathTween);

function pathTween () {
    var interpolate  =d3.scale.quantile()
        .domain([0,1])
        .range(d3.range(1, data.length + 1));
    return function(t) {
        return line(data.slice(0, interpolate(t)));
    };
}


// Add the X Axis
svg.append("g")
    .attr("class", "x axis")
    .attr("transform", "translate(0," + height + ")")
    .call(xAxis);

// Add the Y Axis
svg.append("g")
    .attr("class", "y axis")
    .call(yAxis);

// Add the X grid lines
svg.append("g") 
    .attr("class", "grid")
 
    .style("stroke-dasharray", ("2, 2"))
    .attr("transform", "translate(0," + height +")")
    .call(make_x_axis()
        .tickSize(-height, 0, 0)
    .   tickFormat("")
 )

 // Add the Y grid lines
 svg.append("g")
    .attr("class", "grid")
  
    .style("stroke-dasharray", ("2, 2"))
    .call(make_y_axis()
        .tickSize(-width, 0, 0)
        .tickFormat("")
  )

 // Add a title to a graph
 svg.append("text")
    .attr("x", (width / 2))
    .attr("y", 0 -(margin.top / 2))
    .attr("text-anchor", "middle")
    .attr("font-size", "25px")
    //.style("text-decoration", "underline")
    .text("TEST: temperature measurements in order to control the heating - Drogheda (S.P.)");

</script>
</body>