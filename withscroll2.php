<?php
error_reporting(0);
  $db = new mysqli("172.16.0.87", "devteam", "d3vt3@m" ,"www2-innov8tive");

    $sth = $db->query("SELECT 
            ROUND(Volume,2) as vol,
            ROUND(Open,2) as o,
            ROUND(High,2) as h,
            ROUND(Low,2) as l,
            ROUND(Close,2) as c ,      
            StockDate
              FROM (
              SELECT Volume,Open,High,Low,Close,StockDate 
              FROM StockQuotes 
              GROUP BY StockDate) as avs 
        -- GROUP BY MONTH(StockDate),YEAR(StockDate)
        ORDER BY StockDate ASC");

    $rows = array();
    //flag is not needed
    $flag = true;
    $table = array();

  $table['cols'] = array(
      array('label' => '', 'type' => 'date'),
      array('label' => '','type' => 'number'),
      array('label' => '', 'type' => 'number'),
      array('label' => '','type' => 'number'),
      array('label' => '','type' => 'number'),      
      array('label' => '','type' => 'number')
  );

    $rows = array();

    while($r = $sth->fetch_assoc()) {

    // assumes dates are in the format "yyyy-MM-dd"
    $dateString = $r['StockDate'];
    $dateArray = explode('-', $dateString);
    $year = $dateArray[0];
    $month = $dateArray[1] - 1; // subtract 1 to convert to javascript's 0-indexed months
    $day = $dateArray[2];

    // echo $dateString."<br>";


    echo $timeString;

    $temp = array();
    $temp[] = array('v' => "Date($year, $month, $day)"); 
    $temp[] = array('v' => (string) $r['l']);
    $temp[] = array('v' => (string) $r['o']);
    $temp[] = array('v' => (string) $r['c']);  
    $temp[] = array('v' => (string) $r['h']);  
      
    $temp[] = array('v' => (string) $r['vol']);


    $rows[] = array('c' => $temp);

    }

    $table['rows'] = $rows;
    $jsonTable = json_encode($table);
    /* echo $jsonTable; */  

?>


<script type="text/javascript" src="//www.google.com/jsapi"></script>  
<script type="text/javascript">  
google.load('visualization', '1', { packages : ['controls'] } );  
google.setOnLoadCallback(createTable);  
  
function createTable() {  
  // Create the dataset (DataTable)  
  var myData = new google.visualization.DataTable(<?php echo $jsonTable; ?> );

 // var myData = new google.visualization.DataTable(<?php echo $jsonTable?>);  
  
  // Create a dashboard.  
  var dash_container = document.getElementById('dashboard_div'),  
    myDashboard = new google.visualization.Dashboard(dash_container);  
  
  // Create a date range slider  
  var myDateSlider = new google.visualization.ControlWrapper({  

    'controlType': 'ChartRangeFilter',  
    'containerId': 'control_div',
    'state': {'range': {'start': new Date(2014, 01, 08), 'end': new Date(2014, 02, 07)}},
    'options': {  
      'backgroundColor': '#4D5E70',
      'filterColumnIndex': 0,
      'format': 'd-M-Y',
      'hAxis': {'baselineColor': 'none'}      
    },
     'view': {'columns': [0,1,2,3,4,5]} ,  
      'ui': {
         'minRangeSize': 2592000000
       }
  });  
  
  // Table visualization  
  var myTable = new google.visualization.ChartWrapper({  
    'chartType' : 'Table',  
    'containerId' : 'table_div',
     'view': {'columns': [0,1,2,3,4,5]}    
  });  
  
  // Bind myTable to the dashboard, and to the controls  
  // this will make sure our table is update when our date changes  
   myDashboard.bind(myDateSlider, myTable);  
  
  // Line chart visualization  
  var myLine = new google.visualization.ChartWrapper({  
    'chartType' : 'CandlestickChart',  
    'containerId' : 'line_div',
     'options': {      
      'legend': 'none',
      'chartArea': {'height': '100%', 'width': '50%'},
       'hAxis': {

            'textPosition': 'none',
            'gridlines': {
                color: '#384651',
                count: 7
                
              },
             'textStyle': {
                      color: 'white', 
                      fontName: 'Arial Black', 
                      fontSize: 16
            },
              'baselineColor': '#384651'
       },
      'vAxis': {
            'textStyle':{color: '#384651'},
            'gridlines': {
                color: '#384651',
                count: 10
             },
              'baselineColor': '#384651'
       },
      'backgroundColor': '#FAFAFA',
      'candlestick': {'hollowIsRising' : true,
             'risingColor': {color: "#EB8109",stroke: "#EB8109",fill : "transparent"},
             'fallingColor' : {color: "#88FF00",fill : "#88FF00", stroke: "#88FF00"}},
    } ,
       
          'view': {'columns': [0,1,2,3,4]}   
  });  



    var myBar = new google.visualization.ChartWrapper({  
    'chartType' : 'ColumnChart',  
    'containerId' : 'bar_div',
    'options': {      
      // 'chartArea': {'height': '100%', 'width': '50%'},
      'legend': 'none',
      'backgroundColor': '#FAFAFA',
      // 'bar': {'groupWidth': '90%'},
      'colors':[{color:'#6061BF'}],
      'hAxis': {

            'textStyle':{color: '#384651'},
            'gridlines': {
                color: '#384651',
                count: 7
                
             },
              'baselineColor': '#384651'
       },
      'vAxis': {
            'title': 'Volume',
            'titleTextStyle': {italic: false,color:'#384651',fontSize:'20'},
            'textStyle':{color: '#384651'},
            'gridlines': {
                color: '#384651',
                count: 10
             },
              'baselineColor': '#384651'
       }
    },
    'view': {'columns': [0,5]}  
    
  });  

     myDashboard.bind(myDateSlider, myBar);  

    
  // Bind myLine to the dashboard, and to the controls  
  // this will make sure our line chart is update when our date changes  
  myDashboard.bind(myDateSlider, myLine);  
  
  myDashboard.draw(myData);  
}  
</script>  
 
 <style>

    span{
      font-family: Calibri;
      font-weight: bold;
      text-align: center;
      border: 1px solid white;
      display: inline-block;

    } 

    #over{
      /*margin-bottom:1000px;
      background-color: red;*/
      width: 750px;
      position: absolute;
      z-index: 10;
      top:0;
      left: 0;
      padding: 5px;
    }

    #table_div{
      position: absolute;
      z-index: 3;
      top:0;
      left: 0;
    }

 </style> 


  <div id="dashboard_div">  

      <div style="border:1px solid #384651;width:55%;padding:10px">
        <div id="line_div" style="width:700px;"></div>  
        <div id="bar_div" style="width:700px;margin-top:0px;"></div> 
       
        <div id="control_div" style="height:50px;width:700px;"></div> 
      </div>

       <div style="position:relative;background-color:#F3F6FA;"> 
        <div id="over"><span style="width:23%;">Stock Date</span><span style="width:12%;">Low</span><span style="width:12%;">Open</span><span style="width:12%;">Close</span><span style="width:10%;">High</span><span style="width:23%;">Volume</span></div>
        <div id="table_div" style="width:700px;" style="z-index: -1;"></div>  
      </div>

  </div>  

<!--    <div id="dashboard_div">  

      <div>
        <div id="line_div"></div>  
        <div id="bar_div"></div> 
       
        <div id="control_div"></div> 
      </div>

       <div> 
        <div id="over"><span style="width:23%;">Stock Date</span><span style="width:12%;">Low</span><span style="width:12%;">Open</span><span style="width:12%;">Close</span><span style="width:10%;">High</span><span style="width:23%;">Volume</span></div>
        <div id="table_div"></div>  
      </div>

  </div>   -->