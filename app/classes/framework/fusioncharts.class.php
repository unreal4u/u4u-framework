<?php
/*
$Rev: 438 $
$Date: 2011-01-14 11:07:33 -0300 (Fri, 14 Jan 2011) $
$Author: unreal4u $

   FUSIONCHARTS FREE API PHP CLASS
   Author  :  Infosoft Global Pvt. Ltd.
   version :  FREE
   Company :  Infosoft Global Pvt. Ltd.

   *  Version: 1.0.1 (11 December 2008) [ Fix PHP Short Tag, Function addDatasetsFromDatabase
   *                                    Modifiaction for Transposed ,
   *                                    Fix Transparent setting, FusionCharts.php insight Code
   *                                    setInitParam Function Add, addColors Function Add,
   *                                    encodeXMLChars Function Add ]

   FusionCharts Class easily handles All FusionCharts XML Structures like
   graph, categories, dataset, set, Trend Lines, [vline, styles (for Future)] etc.
   It’s easy to use, it binds data into FusionCharts XML Structures
 */

/**
 * FusionCharts Free API PHP Class
 * @package Miscelaneos
 * @author Infosoft Global Pvt. Ltd. modded by unreal4u
 * @version FREE
 * @version 1.0.1
 */

class FusionCharts {
  public $chartType;                # Chart Friendly Name
  public $chartID;		    # ID of the Chart for JS interactivity(optional)
  public $SWFFile; 		    # Name of the required FusionCharts SWF file
  public $SWFPath = 'im/graficos/'; # relative path of FusionCharts SWF files
  public $width = '800';            # FusionCharts width
  public $height = '480';           # FusionCharts height
  public $del = ';';                # attribute Delimiter
  private $strXML = '';             # Chart XML string
# Chart Series Types : 1 => single series, 2=> multi-series, 5=>Gantt (
# For Future Use : 3=> scatter and bubble, 4=> MSStacked
  private $seriesType;
  public $chartParams = array();    #List of Chart Parameters
  public $chartParamsCounter;	    #Number of Chart parameters

  private $categoriesParam = '';    # Categories Parameter Setting
  public $categoryNames = array();  # Category array for storing Category set
  private $categoryNamesCounter = 1;# Category array counter

  private $dataset = array();         # dataset array
  private $datasetParam = array();    # dataset parameter setting array
  private $datasetCounter = 0;      # dataset array counter
  private $setCounter = 0;          # set array counter

  private $trendLines = array();      # trendLines array
  private $tLineCounter = 1;        # trendLines array counter

  private $chartMSG = '';           #chart messages

  private $chartSWF = array();	# Charts SWF array
  private $arr_FCColors = array(); # Color Set to be applied to dataplots
  private $UserColorON = FALSE;     # User define color define true or false
  private $userColorCounter = 0;
  private $noCache = FALSE;         # Cache Control
  private $DataBaseType = 'mysql';  # DataBase Type
  private $encodeChars = TRUE;      # XML for dataXML or dataURL

#############============ For Gantt Chart  ============================
# Gantt categories
  private $GT_categories = array();
  private $GT_categories_Counter = 0;
  private $GT_categoriesParam = array();

  private $GT_subcategories_Counter = 0;

  private $GT_processes = array();
  private $GT_processes_Counter = 0;
  private $GT_processes_Param = '';

  private $GT_Tasks = array();
  private $GT_Tasks_Counter = 0;
  private $GT_Tasks_Param = '';

  private $GT_Connectors = array();
  private $GT_Connectors_Counter = 0;
  private $GT_Connectors_Param = '';

  private $GT_Milestones = array();
  private $GT_Milestones_Counter = 0;
  private $GT_Milestones_Param = '';

  private $GT_datatable = array();
  private $GT_datatable_Counter = 0;
  private $GT_datatableParam = '';
  private $GT_dataColumnParam = array();

  private $GT_subdatatable_Counter = 0;

#------- For Futute Use (start)----------------
  private $GT_legend = array();
  private $GT_legend_Counter = 0;
  private $GT_legend_Param = '';
#------- For Futute Use (end)----------------
  private $wMode;

  private $JSC = array('debugmode' => FALSE, 'registerwithjs' => FALSE, 'bgcolor' => '', 'scalemode' => 'noScale', 'lang' => 'ES'); # Advanced Chart settings
#############============ For Future Use (start)============================
  private $MSSDataset = array();       # dataset array for MSStackedColumn2D
  private $MSSDatasetParams = array();  # MSSDataset parameter setting
  private $MSSDatasetCounter = 0;          # MSSDataset array counter
  private $MSSSubDatasetCounter = 0;       # ms sub dataset array counter
  private $MSSSetCounter = 0;              # msset array counter

  private $lineSet = array();         # lineSet array
  private $lineSetParam = array();    # lineSet Parameter setting array

  private $lineCounter = 0;               # line array counter
  private $lineSetCounter = 0;            # lineset array counter
  private $lineIDCounter = 0;             # lineID counter;

  private $vtrendLines = array();     # vtrendLines array
  private $vtLineCounter = 1;             # vtrendLines array counter

  private $styles = array();           # styles array
  private $styleDefCounter = 1;                # define counter
  private $styleAppCounter = 1;                # apply counter
#############============ For Future Use (end)============================
# FusionCharts Constructor, its take 4  Parameters.
# when we create object of FusionCharts, then Constructor will auto run and initialize
# chats array parameter like chartType, width, height, chartsID
  public function __construct($chartType="Line",$width="800",$height="480",$chartID="",$isTransparent=""){
    global $app;
    if (!in_array('FusionCharts.js',$app->js)) $app->addJavascriptFile('FusionCharts.js');
    if (empty($_SESSION['chartcount'])) $_SESSION['chartcount'] = 0;
    $this->wMode=$isTransparent;
    $this->setChartArrays();                 # Setting All Charts Array
    $this->colorInit();                      # Initialise colorList
    $this->chartType=strtolower($chartType); # Setting Chart name
    $this->getSeriesType();                  # Getting Charts Series Type
    $this->width=$width;
    $this->height=$height;
    if ($chartID=="") {
      if($_SESSION['chartcount'] <= 0) $_SESSION['chartcount']=1;
      $this->chartID=$chartType.$_SESSION['chartcount'];
      $_SESSION['chartcount']++;
    }
    else $this->chartID=$chartID;
    $this->SWFFile = $this->SWFPath.$this->chartSWF[$this->chartType][0].".swf";
    $this->createCategory($this->categoryNamesCounter); # Creating Category Array

    if($this->seriesType>1) $this->setCounter++; # Initialize Dataset Variables
    if($this->seriesType==3) $this->createvTrendLines($this->vtLineCounter); # vTrendLines Array inisialize
    $this->createTrendLines($this->tLineCounter); # TrendLines Array inisialize
    $this->createStyles("definition"); # Styles Array inisialize
    $this->createSubStyles("definition","style");
    $this->createSubStylesParam("definition","style",$this->styleDefCounter);
  }

##------------ PUBLIC FUNCTIONS ----------------------------------------------------------------
# Special Character
  public function encodeXMLChars($option=true) {
    $this->$encodeChars=$option;
  }

# Setting Parameter Delimiter, Defult Parameter Separator is ";"
  public function setParamDelimiter($strDel){
    $this->del=$strDel;
  }

# Database type set like ORACLE and MYSQL
  public function setDataBaseType($dbType){
    $this->DataBaseType=strtolower($dbType);
  }

# Setting path of SWF file. file name like FCF_Column3D.swf. where FCF_ is common for all SWF file
  public function setSWFPath($SWFPath = '') {
    if (!empty($SWFPath)) {
      $this->SWFPath=$SWFPath;
      $this->SWFFile=$this->SWFPath.$this->chartSWF[$this->chartType][0] .".swf";
    }
  }

# We can add or change single Chart parameter by setChartParam function
# its take Parameter Name and its Value
  public function setChartParam($paramName, $paramValue){
    $this->chartParams[$paramName]=$this->encodeSpecialChars($paramValue);
  }

# We can add or change Chart parameter sets by setChartParams function
# its take parameterset [ caption=xyz caption;subCaption=abcd abcd abcd;xAxisName=x axis;yAxisName=y's axis;bgColor=f2fec0;animation=1 ]
# Defult Parameter Separator is ";"
  public function setChartParams($strParam){
    $listArray=explode($this->del,$strParam);
    foreach ($listArray as $valueArray) {
      $paramValue=explode("=",$valueArray,2);
      if($this->validateParam($paramValue) === TRUE) $this->chartParams[$paramValue[0]]=$this->encodeSpecialChars($paramValue[1]);
    }
  }

# Setting Categories Parameter into categoriesParam variables
  public function setCategoriesParams($strParam) {
    $this->categoriesParam .= $this->ConvertParamToXMLAttribute($strParam);
  }

# Function addCategoryFromDatabase adding Category from dataset
  public function addCategoryFromDatabase($query_result, $categoryColumn) {
    if($this->DataBaseType=="mysql") {
      # fetching recordset till eof
      while($row = mysql_fetch_array($query_result)) $this->addCategory($row[$categoryColumn],"","" );
    }
    elseif($this->DataBaseType=="oracle") {
      # fetching recordset till eof
      while(OCIFetchInto($query_result, $row, OCI_ASSOC)) $this->addCategory($row[$categoryColumn],"","" );
    }
  }

# Function addCategoryFromArray adding Category from Array
  public function addCategoryFromArray($categoryArray) { # convert array to category set
    foreach ($categoryArray as $value) $this->addCategory($value);
  }

# Function for create set and catagory, dataset , set from array

  public function addChartDataFromArray($dataArray, $dataCatArray='') {
    if(is_array($dataArray)) {
      if ($this->seriesType==1) { # Single series Array  ///// aa[..][..]="name" aa[..][..]="value"
        foreach($dataArray as $arrayvalue) if(is_array($arrayvalue)) {
          if(empty($arrayvalue[2])) $arrayvalue[2] = '';
          else $arrayvalue[2] = ';'.$arrayvalue[2];
          $this->addChartData($arrayvalue[1],"label=".$arrayvalue[0].$arrayvalue[2]);
        }
      }
      else { # Multi series Array
	    if(is_array($dataCatArray)) foreach($dataCatArray as $value) $this->addCategory($value);
        foreach($dataArray as $arrayvalue){
          if(is_array($arrayvalue)){
            $i=0; $aaa[0]=""; $aaa[1]="";
            foreach($arrayvalue as $value){
              if($i>=2) $this->addChartData($value);
              else $aaa[$i]=$value;
              if($i==1) $this->addDataset($aaa[0],$aaa[1]);
              $i++;
            }
          }
        }
      }
    }
  }

/*
  public function addChartData($value="",$params="",$vlineParams = "" ){
    $strSetXML="";
    switch ($this->seriesType){
      case 1:
        $this->dataset[$this->setCounter]=$strSetXML;
        $this->setCounter++;
        break;
      case 2:
        $strSetXML=$this->genSSMSChartDataXML($value,$params,$vlineParams);
        break;
      case 3:
        $strSetXML=$this->genScatterBubbleChartDataXML($value,$params,$vlineParams);
        $this->dataset[$this->datasetCounter]["_" . $this->setCounter]=$strSetXML;
        $this->setCounter++;
        break;
      case 4:
        $strSetXML=$this->genSSMSChartDataXML($value,$params,$vlineParams);
        $this->MSStDataset[$this->MSStDatasetCounter][$this->MSStSubDatasetCounter][$this->MSStSetCounter]=$strSetXML;
        $this->MSStSetCounter++;
        break;
    }
  }
*/

# Function addCategory adding Category and vLine element
  public function addCategory($label="",$catParam="",$vlineParam = "" ) {
    $strCatXML = $strParam = "";
    $label=$this->encodeSpecialChars($label);
    if($vlineParam=="") {
      if($catParam!="") $strParam = $this->ConvertParamToXMLAttribute($catParam);
      $strCatXML ="<category label='".$label."' ".$strParam."/>";
    }
    else {
      $strParam = $this->ConvertParamToXMLAttribute($vlineParam);
      $strCatXML="<vLine " . $strParam . "/>";
    }
    $this->categoryNames[$this->categoryNamesCounter]=$strCatXML;
    $this->categoryNamesCounter++;
  }

# adding dataset array element
  public function addDataset($seriesName, $strParam=""){
    $this->datasetCounter++;
    $this->createDataset($this->datasetCounter);
    $this->setCounter++;
    $this->createDataValues($this->datasetCounter,"_" . $this->setCounter);

    $seriesName = $this->encodeSpecialChars($seriesName);
    $tempParam  = "seriesName='" . $seriesName . "' ".$this->ConvertParamToXMLAttribute($strParam);

    $colorParam = "";
    $pos = strpos(strtolower($tempParam), " color");
    if ($pos === false) $colorParam = " color='" . $this->getColor($this->datasetCounter-1) . "'";
    $this->datasetParam[$this->datasetCounter] = $tempParam . $colorParam;
  }

# Function addChartData adding set data element
  public function addChartData($value="",$setParam="",$vlineParam = "" ) {
    $strSetXML="";
    if($this->seriesType>=1 and $this->seriesType<=2) $strSetXML=$this->setSSMSDataArray($value,$setParam,$vlineParam);
    elseif ($this->seriesType==3)                     $strSetXML=$this->setScatterBubbleDataArray($value,$setParam,$vlineParam);
    elseif (($this->seriesType==4))                   $strSetXML=$this->setSSMSDataArray($value,$setParam,$vlineParam);

    if ($this->seriesType==1) { # Adding xml set to dataset array and Increase set counter
      $this->dataset[$this->setCounter]=$strSetXML;
      $this->setCounter++;
    }
    elseif($this->seriesType>1 and $this->seriesType<4) {
      $this->dataset[$this->datasetCounter]["_" . $this->setCounter]=$strSetXML;
      $this->setCounter++;
    }
    elseif($this->seriesType==4) {
      $this->MSSDataset[$this->MSSDatasetCounter][$this->MSSSubDatasetCounter][$this->MSSSetCounter]=$strSetXML;
      $this->MSSSetCounter++;
    }
  }


# The addDatasetsFromDatabase() function adds dataset and set elements from -
# database, by Default, from MySql recordset. You can use setDatabaseType() function -
# to set the type of database to work on.
  public function addDatasetsFromDatabase($query_result, $ctrlField, $valueField,$datsetParamArray="",$link="") {
    $paramset = $tempContrl = $tempParam = "";
    if(!is_array($datsetParamArray)) $datsetParamArray=array();
    $arrLimit=count($datsetParamArray);
    $i=1;
    if($this->DataBaseType=="mysql"){
      $FieldArray=explode($this->del,$valueField);
      if(count($FieldArray) > 1) {
        while($row = mysql_fetch_array($query_result)) $this->addCategory($row[$ctrlField]);
        $k=0;
        foreach ($FieldArray as $FieldName) {
          if($k<$arrLimit) $tempParam = $datsetParamArray[$k];
          else $tempParam="";
          $this->addDataset($FieldName,$tempParam);
          mysql_data_seek($query_result,0);
          while($row = mysql_fetch_array($query_result)) {
            if($link=="") $paramset="";
            else $paramset="link=" . urlencode($this->getLinkFromPattern($row,$link));
            $this->addChartData($row[$FieldName], $paramset, "");
          }
          $k++;
        }
      }
      else {
        while($row = mysql_fetch_array($query_result)){
          if ($tempContrl!=$row[$ctrlField]) {
            if($i<=$arrLimit) $tempParam = $datsetParamArray[$i-1];
            else $tempParam="";
            $this->addDataset($row[$ctrlField],$tempParam);
            $tempContrl=$row[$ctrlField];
            $i++;
          }
          if($link=="") $paramset="";
          else $paramset="link=" . urlencode($this->getLinkFromPattern($row,$link));
          $this->addChartData($row[$valueField], $paramset, "");
        }
      }
    }
    elseif($this->DataBaseType=="oracle") {
      while(OCIFetchInto($query_result, $row, OCI_ASSOC)){
        if ($tempContrl!=$row[$ctrlField]){
          if($i<=$arrLimit) $tempParam = $datsetParamArray[$i-1];
          else $tempParam="";
          $this->addDataset($row[$ctrlField],$tempParam);
          $tempContrl=$row[$ctrlField];
          $i++;
        }
        if($link=="") $paramset="";
        else $paramset="link=" . urlencode($this->getLinkFromPattern($row,$link));
        $this->addChartData($row[$valueField], $paramset, "");
      }
    }
  }

# addDataFromDatabase funcion take 5 parameter like query_result, label field, value field
# and initialize dataset variables and link
  public function addDataFromDatabase($query_result, $db_field_ChartData,$db_field_CategoryNames="", $strParam="",$LinkPlaceHolder=""){
    $paramset="";
    if($this->DataBaseType=="mysql") {
      while($row = mysql_fetch_array($query_result)) {
        if($LinkPlaceHolder=="") $paramset="";
        else $paramset="link=".urlencode($this->getLinkFromPattern($row,$LinkPlaceHolder));
        if ($strParam="") $strParam=$paramset;
        else $strParam .= ";".$paramset;

        if($db_field_CategoryNames==""){
          $data=@$row[$db_field_ChartData];
          if($strParam!="") $this->addChartData($this->encodeSpecialChars($data),$strParam);
          else $this->addChartData($this->encodeSpecialChars($data));
        }
        else {
          $data=@$row[$db_field_ChartData];
          $label=@$row[$db_field_CategoryNames];
          $this->addChartData($this->encodeSpecialChars($data),"name=" . $this->encodeSpecialChars($label) . ";" .$strParam,"" );
        }
      }
    }
    elseif($this->DataBaseType=="oracle") {
      while(OCIFetchInto($query_result, $row, OCI_ASSOC)){
        if($LinkPlaceHolder=="") $paramset="";
        else $paramset="link=".urlencode($this->getLinkFromPattern($row,$LinkPlaceHolder));
        if ($strParam="") $strParam=$paramset;
        else $strParam .= ";".$paramset;

        if($db_field_CategoryNames=="") {
          $data=@$row[$db_field_ChartData];
          if($strParam!="") $this->addChartData($this->encodeSpecialChars($data),$strParam);
          else $this->addChartData($this->encodeSpecialChars($data));
        }
        else {
          $data=@$row[$db_field_ChartData];
          $label=@$row[$db_field_CategoryNames];
          $this->addChartData($this->encodeSpecialChars($data),"name=" . $this->encodeSpecialChars($label) . ";" .$strParam,"" );
        }
      }
    }
  }

# setTLine create TrendLine parameter
  public function addTrendLine($strParam) {
    $listArray=explode($this->del,$strParam);
    foreach ($listArray as $valueArray) {
      $paramValue=explode("=",$valueArray,2);
      if($this->validateParam($paramValue)) $this->trendLines[$this->tLineCounter][$paramValue[0]]=$this->encodeSpecialChars($paramValue[1]);
    }
    $this->tLineCounter++;
  }


#this function sets chart messages
  public function setChartMessage($strParam){
    $this->chartMSG="?";
    $listArray=explode($this->del,$strParam);
    foreach ($listArray as $valueArray) {
      $paramValue=explode("=",$valueArray,2);
      if($this->validateParam($paramValue)==true) $this->chartMSG .= $paramValue[0]."=".$this->encodeSpecialChars($paramValue[1])."&";
    }
    $this->chartMSG=substr($this->chartMSG,0,strlen($this->chartMSG)-1);
  }

#### - This function is mostly for Future USE -----------------------------
# set JS constructor of FusionCharts.js
  public function setAddChartParams($debugMode, $registerWithJS=0, $c="", $scaleMode="", $lang=""){
    $this->JSC["debugmode"]=$debugMode;
    $this->JSC["registerwithjs"]=$registerWithJS;
    $this->JSC["bgcolor"]=$c;
    $this->JSC["scalemode"]=$scaleMode;
    $this->JSC["lang"]=$lang;
  }

# The function SetInitParam() adds extra chart settings
  public function setInitParam($tname,$tvalue) {
    $trimName= strtolower(str_replace(" ","",$tname));
    $this->JSC[$trimName]=$tvalue;
  }

# getXML render all class arrays to XML output
  public function getXML() {
    $this->strXML = $strChartParam = "";
    $strChartParam=$this->getChartParamsXML();
    if($this->seriesType==1) {
      if(gettype(strpos($this->chartType,"line"))!="boolean" AND strpos($strChartParam,"lineColor")===false) {
        $colorSet=$this->getColor(0);
        $this->setChartParams("lineColor=" . $colorSet );
      }
      if(gettype(strpos($this->chartType,"area"))!="boolean" AND strpos($strChartParam,"areaBgColor")===false) {
        $colorSet=$this->getColor(0);
        $this->setChartParams("areaBgColor=" . $colorSet );
      }
    }
    $this->strXML  =  "<chart bgColor='FFFFFF,99CCFF' bgAlpha='40,30' bgRatio='0,100' bgAngle='360' showBorder='1' ".trim($this->getChartParamsXML()).">";
    if ($this->seriesType >= 0 and $this->seriesType <= 4) {
      $this->strXML .= $this->getCategoriesXML();
      $this->strXML .= $this->getDatasetXML();
      if($this->seriesType==3) $this->strXML .= $this->getvTrendLinesXML();
      if($this->seriesType==4) $this->strXML .= $this->getLinesetXML();
      $this->strXML .= $this->getTrendLinesXML();
      $this->strXML .= $this->getStylesXML();
    }
    else if($this->seriesType == 5) {
      $this->strXML .= $this->getGanttCategoriesXML();
      $this->strXML .= $this->getProcessesXML();
      $this->strXML .= $this->getGanttDatatableXML();
      $this->strXML .= $this->getTasksXML();
      $this->strXML .= $this->getConnectorsXML();
      $this->strXML .= $this->getMilestonesXML();
      $this->strXML .= $this->getTrendLinesXML();
      $this->strXML .= $this->getStylesXML();
      $this->strXML .= $this->getLegendXML();
    }
    $this->strXML .= "</chart>";
    return $this->strXML;
  }

# set wMode
  public function setwMode($isTransparent=""){
    $this->wMode=$isTransparent;
  }

# Function getXML render all class arrays to XML output
  public function renderChart($isHTML=false, $display=false) {
    $this->strXML=$this->getXML();
    $this->SWFFile=$this->SWFPath.$this->chartSWF[$this->chartType][0].".swf";

    if($this->noCache) {
      if($this->chartMSG=="") $this->chartMSG = "?nocache=" . microtime();
      else $this->chartMSG .=  "&nocache=" . microtime();
    }
    if($isHTML==false) $tmp = $this->renderChartJS($this->SWFFile . $this->chartMSG,"",$this->strXML,$this->chartID, $this->width, $this->height,$this->JSC["debugmode"], $this->JSC["registerwithjs"],$this->wMode);
    else $tmp = $this->renderChartHTML($this->SWFFile . $this->chartMSG,"",$this->strXML,$this->chartID, $this->width, $this->height,$this->JSC["debugmode"], $this->JSC["registerwithjs"],$this->wMode);
    if ($display === TRUE) echo $tmp;
    else return $tmp;
  }

# Sets whether chart SWF files are not to be cached
  public function setOffChartCaching($swfNoCache=false) {
    $this->noCache=$swfNoCache;
  }

# Renders Chart form External XML data source
  public function renderChartFromExtXML($dataXML) {
    print $this->renderChartJS($this->SWFFile,"",$dataXML,$this->chartID, $this->width, $this->height, $this->JSC["debugmode"], $this->JSC["registerwithjs"], $this->wMode);
  }

// RenderChartJS renders the JavaScript + HTML code required to embed a chart.
// This function assumes that you've already included the FusionCharts JavaScript class
// in your page.

// $chartSWF - SWF File Name (and Path) of the chart which you intend to plot
// $strURL - If you intend to use dataURL method for this chart, pass the URL as this parameter. Else, set it to "" (in case of dataXML method)
// $strXML - If you intend to use dataXML method for this chart, pass the XML data as this parameter. Else, set it to "" (in case of dataURL method)
// $chartId - Id for the chart, using which it will be recognized in the HTML page. Each chart on the page needs to have a unique Id.
// $chartWidth - Intended width for the chart (in pixels)
// $chartHeight - Intended height for the chart (in pixels)
// $debugMode - Whether to start the chart in debug mode
// $registerWithJS - Whether to ask chart to register itself with JavaScript
// $setTransparent - Transparent mode
  public function renderChartJS($chartSWF, $strURL, $strXML, $chartId, $chartWidth, $chartHeight, $debugMode=false, $registerWithJS=false, $setTransparent="") {
    //First we create a new DIV for each chart. We specify the name of DIV as "chartId"Div.
    //DIV names are case-sensitive.
    // The Steps in the script block below are:
    //  1)In the DIV the text "Chart" is shown to users before the chart has started loading
    //    (if there is a lag in relaying SWF from server). This text is also shown to users
    //    who do not have Flash Player installed. You can configure it as per your needs.
    //  2) The chart is rendered using FusionCharts Class. Each chart's instance (JavaScript) Id
    //     is named as chart_"chartId".
    //  3) Check whether to provide data using dataXML method or dataURL method
    //     save the data for usage below
    if ($strXML=="") $tempData = "chart_$chartId.setDataURL(\"$strURL\");";
    else $tempData = "chart_$chartId.setDataXML(\"$strXML\");";

    $chartIdDiv = $chartId."Div";
    $ndebugMode = $this->boolToNum($debugMode);
    $nregisterWithJS = $this->boolToNum($registerWithJS);
    $nsetTransparent=($setTransparent?"true":"false");

    $strHTML = "<div id=\"$chartIdDiv\" class=\"centrar\" style=\"margin:3px;width:auto\">Chart.</div>";
    $strHTML .= "<script type=\"text/javascript\">";
    $strHTML .= "var chart_$chartId = new FusionCharts(\"$chartSWF\",\"$chartId\",\"$chartWidth\",\"$chartHeight\",\"$ndebugMode\",\"$nregisterWithJS\",\"".$this->JSC["bgcolor"]."\",\"".$this->JSC["scalemode"]."\",\"".$this->JSC["lang"]."\");";
    if($nsetTransparent=="true") $strHTML .= "chart_$chartId.setTransparent(\"$nsetTransparent\");";
    $strHTML .= $tempData."chart_$chartId.render(\"$chartIdDiv\");";
    $strHTML .= "</script>";
    return $strHTML;
  }


    //RenderChartHTML function renders the HTML code for the JavaScript. This
    //method does NOT embed the chart using JavaScript class. Instead, it uses
    //direct HTML embedding. So, if you see the charts on IE 6 (or above), you'll
    //see the "Click to activate..." message on the chart.
    // $chartSWF - SWF File Name (and Path) of the chart which you intend to plot
    // $strURL - If you intend to use dataURL method for this chart, pass the URL as this parameter. Else, set it to "" (in case of dataXML method)
    // $strXML - If you intend to use dataXML method for this chart, pass the XML data as this parameter. Else, set it to "" (in case of dataURL method)
    // $chartId - Id for the chart, using which it will be recognized in the HTML page. Each chart on the page needs to have a unique Id.
    // $chartWidth - Intended width for the chart (in pixels)
    // $chartHeight - Intended height for the chart (in pixels)
    // $debugMode - Whether to start the chart in debug mode
    // $registerWithJS - Whether to ask chart to register itself with JavaScript
    // $setTransparent - Transparent mode
  private function renderChartHTML($chartSWF, $strURL, $strXML, $chartId, $chartWidth, $chartHeight, $debugMode=false,$registerWithJS=false, $setTransparent="") {
    $strHTML="";
    $strFlashVars = "&chartWidth=".$chartWidth."&chartHeight=".$chartHeight."&debugMode=".$this->boolToNum($debugMode)."&scaleMode=".$this->JSC["scalemode"]."&lang=".$this->JSC["lang"];
    if ($strXML=="") $strFlashVars .= "&dataURL=".$strURL;
    else $strFlashVars .= "&dataXML=".$strXML;

    $nregisterWithJS = $this->boolToNum($registerWithJS);
    if($setTransparent!="") $nsetTransparent=($setTransparent==false?"opaque":"transparent");
    else $nsetTransparent="window";

    $HTTP="http";
    if(strtolower($_SERVER['HTTPS'])=="on") $HTTP="https";

    $Strval = $_SERVER['HTTP_USER_AGENT'];
    $pos=strpos($Strval,"MSIE");
    if($pos===false) $strHTML .= "<embed src=\"$chartSWF\" FlashVars=\"$strFlashVars&registerWithJS=$nregisterWithJS\" quality=\"high\" width=\"$chartWidth\" height=\"$chartHeight\" name=\"$chartId\" " . ($this->JSC["bgcolor"]!="")? " bgcolor=\"" . $this->JSC["bgcolor"] . "\"":"" . " allowScriptAccess=\"always\"  type=\"application/x-shockwave-flash\"  pluginspage=\"$HTTP://www.macromedia.com/go/getflashplayer\" wmode=\"$nsetTransparent\" \n";
    else {
      $strHTML .= "<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"$HTTP://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0\" width=\"$chartWidth\" height=\"$chartHeight\" id=\"$chartId\">";
      $strHTML .= "<param name=\"allowScriptAccess\" value=\"always\" />";
      $strHTML .= "<param name=\"movie\" value=\"$chartSWF\" />";
      $strHTML .= "<param name=\"FlashVars\" value=\"$strFlashVars&registerWithJS=$nregisterWithJS\" />";
      $strHTML .= "<param name=\"quality\" value=\"high\"  />";
      $strHTML .= "<param name=\"wmode\" value=\"$nsetTransparent\"  />";
      if($this->JSC["bgcolor"] !="") $strHTML .=  "<param name=\"bgcolor\" value=\"".$this->JSC["bgcolor"]."\" />";
      $strHTML .= "</object>";
    }
    return $strHTML;
  }

    // The function boolToNum() function converts boolean values to numeric (1/0)
  private function boolToNum($bVal) {
    return (($bVal==true) ? 1 : 0);
  }

##------------ PRIVATE FUNCTIONS ----------------------------------------------------------------
# getDatasetXML create set chart xml
  private function getDatasetXML(){
    $output = '';
    switch ($this->seriesType){
      case 1 :
        $output = $this->getSSDatasetXML();
        break;
      case 2 :
        $output = $this->getMSDatasetXML();
        break;
      case 3 :
        $output = $this->getMSDatasetXML();
        break;
      case 4 :
        $output = $this->getMSStackedDatasetXML();
        break;
    }
    return $output;
  }

# By getChartParamsXML, we can fetch charts array and convert into XML
# and return like "caption='xyz' xAxisName='x side' ............
  private function getChartParamsXML(){
    $partXML="";
    foreach($this->chartParams as $part_type => $part_name) $partXML .= $part_type."='".$this->encodeSpecialChars($part_name)."' ";
    return $partXML;
  }

# Function getCategoriesXML for getting Category part XML
  private function getCategoriesXML(){
    $partXML = '';
    if($this->seriesType>1){
      $partXML .= "<categories ".trim($this->categoriesParam).">";
      if($this->categoryNamesCounter>1) foreach($this->categoryNames as $part_type => $part_name) if(!empty($part_name) AND $part_name != 'Array') $partXML .= $part_name;
      $partXML .= "</categories>";
    }
    return $partXML;
  }

# creating single set element
#       <set value='30' />
#       <set value='26' />
  private function getSSDatasetXML(){
    $partXML = "";
    if($this->seriesType==1) foreach($this->dataset as $part_type => $part_name) if(!empty($part_name) AND $part_name != 'Array') $partXML .= $part_name;
    return $partXML;
  }

# getMSDatasetXML for getting datset part XML
#     <dataset seriesName='Product A' color='AFD8F8' showValues='0'>
#       <set value='30' />
#       <set value='26' />
#     </dataset>
  private function getMSDatasetXML(){
    $partXML="";
    if($this->seriesType>1) {
      foreach($this->dataset as $part_type => $part_name) {
        $partXML .="<dataset " . $this->datasetParam[$part_type] . " >";
        foreach($this->dataset[$part_type] as $part_type1 => $part_name1) if(!empty($part_name1) AND $part_name1 != 'Array') $partXML .= $part_name1;
        $partXML .="</dataset>";
      }
    }
    return $partXML;
  }

# getTrendLinesXML create XML output depending on trendLines array
#  <trendLines>
#    <line startValue='700000' color='009933' displayvalue='Target' />
# </trendLines>
  private function getTrendLinesXML() {
    $partXML=""; $lineXML="";
    foreach($this->trendLines as $l_type => $l_name) {
      $lineXML .="<line";
      foreach($this->trendLines[$l_type] as $part_type => $part_name) $lineXML .= ' '.$part_type."='".$this->encodeSpecialChars($part_name)."'";
      $lineXML .="/>";
    }
    if (strpos($lineXML,'=') !== FALSE) $partXML = "<trendLines>".$lineXML."</trendLines>";
    else $partXML="";
    return $partXML;
  }

#adding set element to dataset element for seriesType 1 and 2
  private function setSSMSDataArray($value="",$setParam="",$vlineParam = "" ){
    $strSetXML=""; $strParam=""; $color=0;
    if($vlineParam=="") {
      if($setParam!="") $strParam = $this->ConvertParamToXMLAttribute($setParam);
      $colorSet="";
      if ($this->UserColorON == true AND $this->seriesType==1 AND (gettype(strpos($this->chartType,"line"))=="boolean" && gettype(strpos($this->chartType,"area"))=="boolean") AND strpos(strtolower($strParam)," color")===false) $colorSet=" color='" . $this->getColor($this->setCounter) . "' ";
      else if($this->seriesType==1 && (gettype(strpos($this->chartType,"pie"))=="boolean" && gettype(strpos($this->chartType,"line"))=="boolean" && gettype(strpos($this->chartType,"area"))=="boolean") AND strpos(strtolower($strParam)," color")===false) $colorSet=" color='" . $this->getColor($this->setCounter) . "' ";
      $strSetXML ="<set value='".$value."' ".trim($strParam.$colorSet)."/>";
    }
    else {
      $strParam = $this->ConvertParamToXMLAttribute($strParam);
      $strSetXML="<vLine ".$strParam." />";
    }
    return $strSetXML;
  }

## - - - -   - -   Array Init Functions  - - --- - -- - - - - - - -- - - - - -
# Function createCategory create array element with in Categories
  private function createCategory($catID){
    $this->categoryNames[$catID]= array();
  }
# createDataset dataset array element
  private function createDataset($dataID){
    $this->dataset[$dataID]= array();
  }
# creating set  dataset array element
  private function createDataValues($datasetID, $dataID){
    $this->dataset[$datasetID][$dataID]= array();
  }
# createTrendLines create TrendLines array
  private function createTrendLines($lineID){
    $this->trendLines[$lineID] = array();
  }
# setTLine create TrendLine parameter
  private function setTLine($lineID,$paramName, $paramValue){
    $this->trendLines[$lineID][$paramName]=$paramValue;
  }

# --------------------  Misc utility functions  ---------------------
# converting ' and " to %26apos; and &quot;
  private function encodeSpecialChars($strValue){
    $strValue=preg_replace("/%(?![\da-f]{2}|[\da-f]{4})/i", "%25", $strValue);
    $original = array("&","'","\"",'<a','</a','<i','</i','<u','</u','<li','</li','<font','</font','<p','</p','<br','<b','</b','<','>','=','+','¢',"£","€","¥","₣");
    if ($this->encodeChars==true) $replace = array("%26","%26apos;","%26quot;","%26lt;A","%26lt;/A",'%26lt;I','%26lt;/I','%26lt;U','%26lt;/U','%26lt;LI','%26lt;/LI','%26lt;FONT','%26lt;/FONT','%26lt;P','%26lt;/P','%26lt;BR','%26lt;B','%26lt;/B','%ab','%26gt;',"%3d","%2b","%a2","%a3","%E2%82%AC","%a5","%e2%82%a3");
    else $replace = array('&apos;','&quot;','&lt;A',"&lt;/A","&lt;I","&lt;/I","&lt;U","&lt;/U","&lt;LI","&lt;/LI","&lt;FONT","&lt;/FONT","&lt;P","&lt;/P","&lt;BR","&lt;B","&lt;/B","%ab",'&gt;',"%3d","%2b","%a2","%a3","%E2%82%AC","%a5","%e2%82%a3");
    $strValue = str_ireplace($original,$replace,$strValue);
    return $strValue;
  }

# Its convert pattern link to original link
# abcd.php?cid=##Field_name_1##&pname=##Field_name_2##
  private function getLinkFromPattern($row,$tempLink){
    $aa=explode("##",$tempLink);
    foreach($aa as $v) {
      $pos = strpos($v,"=");
      if($pos === false AND !empty($v)){
        $pet="##" . $v . "##";
        $tempLink=str_replace($pet,$row[$v],$tempLink);
      }
    }
    return $tempLink;
  }

# Convertion of semi colon(;) separeted paramater to XML attribute
  private function ConvertParamToXMLAttribute($strParam){
    $xmlParam="";
    $listArray=explode($this->del,$strParam);
    foreach ($listArray as $valueArray) {
      $paramValue=explode("=",$valueArray,2);
      if($this->validateParam($paramValue)==true) $xmlParam .= $paramValue[0] . "='" . $this->encodeSpecialChars($paramValue[1]) . "' ";
    }
    return $xmlParam;
  }

# Does some sort of parameters validation
  private function validateParam($paramValue) {
    $output = FALSE;
    if(count($paramValue) >= 2 AND trim($paramValue[0]) != "") $output = TRUE;
    return $output;
  }

# Getting Charts series type from charts array. 1 => single series, 2=> multi-series, 3=> scatter and bubble, 4=> MSStacked. defult 1 => single series
  private function getSeriesType(){
    if(is_array($this->chartSWF[$this->chartType])) $this->seriesType=$this->chartSWF[$this->chartType][1];
    else $this->seriesType=1;
  }

# This function returns a color from a list of colors
  private function getColor($counter) {
    $strColor="";
    if ($this->UserColorON == false) $strColor=$this->arr_FCColors[$counter % count($this->arr_FCColors)];
    else $strColor=$this->UserColor[$counter % count($this->UserColor)];
    return $strColor;
  }

# Clear User Color
  private function ClearUserColor() {
    $this->UserColorON = false;
  }

# Add User Colors
  private function addColors($ColorList) {
    $listArray=explode($this->del, $ColorList);
    $this->UserColorON = true;
    foreach ($listArray as $valueArray) {
      $this->UserColor[$this->userColorCounter]=$valueArray;
      $this->userColorCounter++;
    }
  }

### ----- Pupulate Color and Chart SWF array  ------ ------- ---------------------
  private function colorInit() {
    $this->arr_FCColors = array("F46A25","AFD8F8","F6BD0F","8BBA00","FF8E46","008E8E","D64646","8E468E","588526","B3AA00","008ED6","9D080D","A186BE","CC6600","FDC689","ABA000","F26D7D","FFF200","0054A6","F7941C","CC3300","006600","663300","6DCFF6");
  }

# Setting FusionCharts SWF file array list and series
  private function setChartArrays(){
    $this->chartSWF = array(
'area2d'                  => array('Area2D',                 1),
'bar2d'                   => array('Bar2D',                  1),
'column2d'                => array('Column2D',               1),
'column3d'                => array('Column3D',               1),
'doughnut2d'              => array('Doughnut2D',             1),
'doughnut3d'              => array('Doughnut3D',             1),
'line'                    => array('Line',                   1),
'pie2d'                   => array('Pie2D',                  1),
'pie3d'                   => array('Pie3D',                  1),
'funnel'                  => array('Funnel',                 1),
'msarea'                  => array('MSArea',                 2),
'msarea2d'                => array('MSArea2D',               2),
'msbar2d'                 => array('MSBar2D',                2),
'mscolumn2d'              => array('MSColumn2D',             2),
'mscolumn3d'              => array('MSColumn3D',             2),
'mscolumn3dlinedy'        => array('MSColumn3DLineDY',       2),
'mscolumnLine3D'          => array('MSColumnLine3D',         2),
'mscombi2d'               => array('MSCombi2D',              2),
'mscombidy2d'             => array('MSCombiDY2D',            2),
'msline'                  => array('MSLine',                 2),
'scrollarea2d'            => array('ScrollArea2D',           2),
'scrollcolumn2d'          => array('ScrollColumn2D',         2),
'scrollcombi2d'           => array('ScrollCombi2D',          2),
'scrollcombidy2d'         => array('ScrollCombiDY2D',        2),
'scrollline2d'            => array('ScrollLine2D',           2),
'scrollstackedcolumn2d'   => array('ScrollStackedColumn2D',  2),
'stackedarea2d'           => array('StackedArea2D',          2),
'stackedbar2d'            => array('StackedBar2D',           2),
'stackedbar3d'            => array('StackedBar3D',           2),
'stackedcolumn2d'         => array('StackedColumn2D',        2),
'stackedcolumn3d'         => array('StackedColumn3D',        2),
'stackedcolumn3dlinedy'   => array('StackedColumn3DLineDY',  2),
'mscolumn2dlinedy'        => array('MSColumn2DLineDY',       2),
'bubble'                  => array('Bubble',                 3),
'scatter'                 => array('Scatter',                3),
'msstackedcolumn2dlinedy' => array('MSStackedColumn2DLineDY',4),
'msstackedcolumn2d'       => array('MSStackedColumn2D',      2),
'gantt'                   => array('Gantt',                  5),
    );
  }

####################### GANTT CHART  (start) ######################################
# ----------- Public Functions -----------------------------------------------
# Function addCategory adding Category and vLine element
  public function addGanttCategorySet($catParam="") {
    $this->GT_categories_Counter++;
    $this->GT_categories[$this->GT_categories_Counter]= array();
    $strParam="";

    if(!empty($catParam)) $strParam = $this->ConvertParamToXMLAttribute($catParam);
    $this->GT_categoriesParam[$this->GT_categories_Counter]=$strParam;
  }

# Function addGanttCategory adding Category
  public function addGanttCategory($label="",$catParam="") {
    $strCatXML=""; $strParam="";
    if(!empty($catParam)) $strParam = $this->ConvertParamToXMLAttribute($catParam);
    $strCatXML ="<category name='".$label."' ".trim($strParam)."/>";

    $this->GT_categories[$this->GT_categories_Counter][$this->GT_subcategories_Counter]=$strCatXML;
    $this->GT_subcategories_Counter++;
  }

# Setting Process Parameter into categoriesParam variables
  public function setGanttProcessesParams($strParam) {
    $this->GT_processes_Param .= $this->ConvertParamToXMLAttribute($strParam);
  }

# Function addGanttProcess adding Process
  public function addGanttProcess($label="",$catParam="") {
    $strCatXML=""; $strParam="";
    if(!empty($catParam)) $strParam = $this->ConvertParamToXMLAttribute($catParam);
    $strCatXML ="<process name='" . $label . "' " . trim($strParam) . "/>";
    $this->GT_processes[$this->GT_processes_Counter]=$strCatXML;
    $this->GT_processes_Counter++;
  }

# Setting Tasks Parameter into TaskParam variables
  public function setGanttTasksParams($strParam){
    $this->GT_Tasks_Param .= $this->ConvertParamToXMLAttribute($strParam);
  }

# Function addGanttTasks adding Tasks
  public function addGanttTask($label="",$catParam=""){
    $strCatXML=""; $strParam="";
    if(!empty($catParam)) $strParam = $this->ConvertParamToXMLAttribute($catParam);
    $strCatXML ="<task name='" . $label . "' " . $strParam . "/>";
    $this->GT_Tasks[$this->GT_Tasks_Counter]=$strCatXML;
    $this->GT_Tasks_Counter++;
  }

# Setting Tasks Parameter into ConnectorsParam variables
  public function setGanttConnectorsParams($strParam){
    $this->GT_Connectors_Param .= $this->ConvertParamToXMLAttribute($strParam);
  }

# Function addGanttConnector adding Connector
  public function addGanttConnector($From,$To,$catParam=""){
    $strCatXML=""; $strParam="";
    if(!empty($catParam)) $strParam = $this->ConvertParamToXMLAttribute($catParam);
    $strCatXML ="<connector fromTaskId='" . $From . "'  toTaskId='" . $To . "' " . $strParam . "/>";
    $this->GT_Connectors[$this->GT_Connectors_Counter]=$strCatXML;
    $this->GT_Connectors_Counter++;
  }

# Setting Milestones Parameter into MilestonesParam variables
  public function setGanttMilestonesParams($strParam){
    $this->GT_Milestones_Param .= $this->ConvertParamToXMLAttribute($strParam);
  }

# Function addGanttMilestones adding Milestones
  public function addGanttMilestone($taskID,$catParam=""){
    $strCatXML=""; $strParam="";
    if(!empty($catParam)) $strParam = $this->ConvertParamToXMLAttribute($catParam);
    $strCatXML ="<milestone taskId='" . $taskID . "'  " . $strParam . "/>";
    $this->GT_Milestones[$this->GT_Milestones_Counter]=$strCatXML;
    $this->GT_Milestones_Counter++;
  }

# Setting Legend Parameter into LegendParam variables
  public function setGanttLegendParams($strParam){
    $this->GT_legend_Param .= $this->ConvertParamToXMLAttribute($strParam);
  }

# Function addGanttLegendItem adding LegendItem
  public function addGanttLegendItem($label,$catParam=""){
    $strCatXML=""; $strParam="";
    if(!empty($catParam)) $strParam = $this->ConvertParamToXMLAttribute($catParam);
    $strCatXML ="<item label='" . $label . "'  " . $strParam . "/>";
    $this->GT_legend[$this->GT_legend_Counter]=$strCatXML;
    $this->GT_legend_Counter++;
  }

# Setting Datatable Parameter into DatatableParam variables
  public function setGanttDatatableParams($strParam){
    $this->GT_datatableParam .= $this->ConvertParamToXMLAttribute($strParam);
  }

# Function addGanttDatacolumn adding Datacolumn
  public function addGanttDatacolumn($catParam=""){
    $this->GT_datatable_Counter++;
    $this->GT_datatable[$this->GT_datatable_Counter]= array();
    $strParam="";
    if(!empty($catParam)) $strParam = $this->ConvertParamToXMLAttribute($catParam);
    $this->GT_dataColumnParam[$this->GT_datatable_Counter]=$strParam;
  }

# Function addGanttColumnText adding ColumnText
  public function addGanttColumnText($label="",$catParam=""){
    $strCatXML=""; $strParam="";
    if(!empty($catParam)) $strParam = $this->ConvertParamToXMLAttribute($catParam);
    $strCatXML ="<text label='" . $label . "' " . $strParam . "/>";
    $this->GT_datatable[$this->GT_datatable_Counter][$this->GT_subdatatable_Counter]=$strCatXML;
    $this->GT_subdatatable_Counter++;
  }

### ------------- Gantt Private Functoins ----------------------------------------------------------------------
#-- Gantt array init ------------------------------------------------
# Function createCategory create array element with in Categories
  private function createGanttCategory($catID){
    $this->GT_categories[$catID]= array();
  }

# Function createGanttDatatable create array element with in Datatable
  private function createGanttDatatable($catID){
    $this->GT_datatable[$catID]= array();
  }

#-- GANTT XML genetators -------------------------------------------
# Function getCategoriesXML for getting Category part XML
  private function getGanttCategoriesXML(){
    $partXML="";
    foreach($this->GT_categories as $part_type => $part_name){
      $partXML .="<categories " . trim($this->GT_categoriesParam[$part_type]) . ">";
      foreach($this->GT_categories[$part_type] as $part_type1 => $part_name1) if(!empty($part_name1)) $partXML .= $part_name1;
      $partXML .="</categories>";
    }
    return $partXML;
  }

# Function getProcessesXML for getting Processes part XML
  private function getProcessesXML() {
    $partXML="";
    $partXML="<processes " . $this->GT_processes_Param . ">"; # adding processes parameter
    foreach($this->GT_processes as $part_type => $part_name) if(!empty($part_name)) $partXML .= $part_name;
    $partXML .="</processes>";
    return $partXML;
  }

# Function getProcessesXML for getting Processes part XML
  private function getTasksXML() {
    $partXML="";
    $partXML="<tasks " . $this->GT_Tasks_Param . ">";
    foreach($this->GT_Tasks as $part_type => $part_name) if(!empty($part_name)) $partXML .= $part_name;
    $partXML .="</tasks>";
    return $partXML;
  }

# Function getConnectorsXML for getting Connectors part XML
  private function getConnectorsXML() {
    $c=0; $partXML="";
    $partXML="<connectors ".$this->GT_Connectors_Param.">";
    foreach($this->GT_Connectors as $part_type => $part_name) {
      if(!empty($part_name)) {
        $partXML .= $part_name;
        $c++;
      }
    }
    $partXML .="</connectors>";
    if ($c>0) return $partXML;
    else return "";
  }

# Function getMilestonesXML for getting Milestones part XML
  private function getMilestonesXML(){
    $c=0; $partXML="";
    $partXML="<milestones ".$this->GT_Milestones_Param.">";
    foreach($this->GT_Milestones as $part_type => $part_name) {
      if(!empty($part_name)) {
        $partXML .= $part_name;
        $c++;
      }
    }
    $partXML .="</milestones>";
    if ($c>0) return $partXML;
    else return "";
  }

# Function getLegendXML for getting Legend part XML
  private function getLegendXML(){
    $c=0; $partXML="";
    $partXML="<legend ".$this->GT_legend_Param.">";
    foreach($this->GT_legend as $part_type => $part_name){
      if(!empty($part_name)){
        $partXML .= $part_name;
         $c++;
      }
    }
    $partXML .="</legend>";
    if ($c>0) return $partXML;
    else return "";
  }

# Function getGanttDatatableXML for getting Category part XML
  private function getGanttDatatableXML() {
    $partXML="";
    foreach($this->GT_datatable as $part_type => $part_name) {
      $partXML .="<dataColumn ".$this->GT_dataColumnParam[$part_type] . ">";
      foreach($this->GT_datatable[$part_type] as $part_type1 => $part_name1) if(!empty($part_name1)) $partXML .= $part_name1;
      $partXML .="</dataColumn>";
    }
    $allPart="<dataTable " . $this->GT_datatableParam . ">" . $partXML . "</dataTable>";
    return $allPart;
  }

####################### GANTT CHART  (end) ######################################
#====================== For Future Use (start) =====================================
##---------PUBLIC functions ----------------------------------------------------
  public function addLineset($seriesName, $strParam){
    $this->createLineset(); $this->lineSetCounter++;
    $this->lineSet[$this->lineCounter][$this->lineSetCounter]= array();
    $tempParam ="seriesName='" . $seriesName . "' ".$this->ConvertParamToXMLAttribute($strParam);
    $this->lineIDCounter++;
    $this->lineSetParam [$this->lineSetCounter]=$tempParam;
  }

# adding Line's Set data
  public function addLinesetData($value="",$setParam="",$vlineParam = "" ){
    $strSetXML=$this->setSSMSDataArray($value,$setParam,$vlineParam);
    $this->lineSet[$this->lineCounter][$this->lineSetCounter][$this->lineIDCounter]=$strSetXML;
    $this->lineIDCounter++;
  }

# adding ms dataset and parameter
  public function addMSSSubDataset($seriesName, $strParam){
    $this->MSSSubDatasetCounter++;
    $this->MSSDataset[$this->MSSDatasetCounter][$this->MSSSubDatasetCounter]= array();
    $tempParam ="seriesName='" . $seriesName . "' ".$this->ConvertParamToXMLAttribute($strParam);
    $this->MSSSetCounter++;
    $this->MSSDatasetParams[$this->MSSDatasetCounter][$this->MSSSubDatasetCounter]=$tempParam;
  }

# adding set element to dataset element for seriesType 3
  public function setScatterBubbleDataArray($value="",$setParam="",$vlineParam = "" ){
    $strSetXML=""; $strParam="";
    if(empty($vlineParam)) {
      if(!empty($setParam)) $strParam = $this->ConvertParamToXMLAttribute($setParam);
      $strSetXML ="<set x='" . $value . "' " . $strParam . "/>";
    }
    else {
      $strParam = $this->ConvertParamToXMLAttribute($vlineParam);
      $strSetXML="<vLine " . $strParam . "/>";
    }
    return $strSetXML;
  }

# setvTLine create TrendLine parameter
  public function setVTrendLines($strParam){
    $listArray=explode($this->del,$strParam);
    foreach ($listArray as $valueArray) {
      $paramValue=explode("=",$valueArray,2);
      if($this->validateParam($paramValue)==true) $this->vtrendLines[$this->vtLineCounter][$paramValue[0]]=$this->encodeSpecialChars($paramValue[1]);
    }
    $this->vtLineCounter++;
  }

# setSubStylesParam create sub styles array to store parameters
  public function addStyleDef($styleName,$styleType,$strParam){
    $this->styles["definition"]["style"][$this->styleDefCounter]["name"]= $styleName;
    $this->styles["definition"]["style"][$this->styleDefCounter]["type"]= $styleType;
    $listArray=explode($this->del,$strParam);
    foreach ($listArray as $valueArray) {
      $paramValue=explode("=",$valueArray,2);
      if($this->validateParam($paramValue)==true) $this->styles["definition"]["style"][$this->styleDefCounter][$paramValue[0]]= $this->encodeSpecialChars($paramValue[1]);
    }
    $this->styleDefCounter++;
  }

# Apply styles
  public function addStyleApp($toObject,$styles){
    $this->styles["application"]["apply"][$this->styleAppCounter]["toObject"]= $toObject;
    $this->styles["application"]["apply"][$this->styleAppCounter]["styles"]= $styles;
    $this->styleAppCounter++;
  }

##---------PRIVATE functions ----------------------------------------------------
## - --  - - XML generators  - - - - ---- - -- - - - -
# Function getLinesetXML for getting Lineset XML
  private function getLinesetXML(){
    $partXML="";
    if($this->seriesType==4) {
      foreach($this->lineSet as $part_type => $part_name){
        $partXML .= "<lineset " . trim($this->lineSetParam[$part_type])   . ">";
        foreach($this->lineSet[$part_type] as $part_type1 => $part_name1)
          foreach($this->lineSet[$part_type][$part_type1] as $part_type2 => $part_name2) if (!empty($part_type2)) $partXML .= $part_name2;
        $partXML .= "</lineset>";
      }
    }
    return $partXML;
  }

# Function getMSStackedDatasetXML for getting datset part XML from ms stacked chart dataset array
# <dataset>
#     <dataset seriesName='Product A' color='AFD8F8' showValues='0'>
#       <set value='30' />
#       <set value='26' />
#     </dataset>
# </dataset>
  private function getMSStackedDatasetXML(){
    $partXML="";
    if($this->seriesType==4){
      foreach($this->MSSDataset as $part_type => $part_name){
        $partXML .= "<dataset>";
        foreach($this->MSSDataset[$part_type] as $part_type1 => $part_name1){
          $partXML .= "<dataset " . $this->MSSDatasetParams[$part_type][$part_type1] . ">";
          foreach($this->MSSDataset[$part_type][$part_type1] as $part_type2 => $part_name2) if ($part_type2!="") $partXML .= $part_name2;
          $partXML .= "</dataset>";
        }
        $partXML .= "</dataset>";
      }
    }
    return $partXML;
  }

# getvTrendLinesXML create XML output depending on trendLines array
#  <vTrendlines>
#    <line displayValue='vTrendLines' startValue='5' endValue='6' alpha='10' color='ff0000'  />
# </vTrendlines>
  private function getvTrendLinesXML(){
    $partXML = $lineXML = "";
    foreach($this->vtrendLines as $l_type => $l_name){
      $lineXML .="<line";
      foreach($this->vtrendLines[$l_type] as $part_type => $part_name) $lineXML .= ' '.$part_type . "='" . $this->encodeSpecialChars($part_name) . "'";
      $lineXML .="/>";
    }
    $pos = strpos($lineXML, "=");
    if ($pos !== false) $partXML = "<vTrendlines>" . $lineXML . "</vTrendlines>";
    else $partXML="";
    return $partXML;
  }
# getStylesXML create the styles XML from styles array
/*
<styles>
  <definition>
     <style name='CanvasAnim' type='animation' param='_xScale' start='0' duration='1' />
  </definition>
  <application>
     <apply toObject='Canvas' styles='CanvasAnim' />
  </application>
</styles>
*/
  private function getStylesXML(){
    $partXML = $lineXML = "";
    foreach($this->styles as $s_type => $s_name){
      $lineXML .="<" . $s_type . ">";
      foreach($this->styles[$s_type] as $sub_type => $sub_name){
        foreach($this->styles[$s_type][$sub_type] as $part_type => $part_name){
          $lineXML .="<" . $sub_type . " ";
          foreach($this->styles[$s_type][$sub_type][$part_type] as $part_type1 => $part_name1) $lineXML .= $part_type1 . "='" . $this->encodeSpecialChars($part_name1) . "' ";
          $lineXML .="/>";
        }
      }
      $lineXML .="</" . $s_type .  ">";
    }
    $pos = strpos($lineXML, "=");
    if ($pos !== false) $partXML = "<styles>" . $lineXML . "</styles>";
    else $partXML ="";
    return $partXML;
  }

## ---------- Array Init functions ----------------------------------------------
# create Lineset array
  private function createLineset(){
    $this->lineCounter++;
    $this->lineSet[$this->lineCounter]= array();
  }

# creating MS-Stacked ataset array element and parameter array
  private function createMSSDataset(){
    $this->MSSDatasetCounter++;
    $this->MSSDataset[$this->MSSDatasetCounter]= array();
    $this->MSSDatasetParams[$this->MSSDatasetCounter]=array();
  }

# Creating set data with in datset
  private function createMSSSetData(){
    $this->MSSSetCounter++;
    $this->MSSDataset[$this->MSSDatasetCounter][$this->MSSSubDatasetCounter][$this->MSSSetCounter]= array();
  }

# createStyles create array element with in styles array
  private function createStyles($styleID){
    $this->styles[$styleID]= array();
  }

# createSubStyles create array element with in styles array element with in sub styles array
# element for storing sub element parameter
  private function createSubStyles($styleID,$subStyle){
    $this->styles[$styleID][$subStyle]= array();
  }

# createvTrendLines create TrendLines array
  private function createvTrendLines($lineID){
    $this->vtrendLines[$lineID] = array();
  }

# setvTLine create TrendLine parameter
  private function setvTLine($lineID,$paramName, $paramValue){
    $this->vtrendLines[$lineID][$paramName]=$paramValue;
  }

# create sub styles param
  private function createSubStylesParam($styleID,$subStyle,$subParam){
    $this->styles[$styleID][$subStyle][$subParam]= array();
  }

# setSubStylesParam create sub styles array to store parameters
  private function setSubStylesParam($styleID,$subStyle,$subParam,$id,$value){
    $this->styles[$styleID][$subStyle][$subParam][$id]= $value;
  }
#====================== For Future Use (end) ======================================
}
