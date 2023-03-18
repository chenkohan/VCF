<?php
	require_once 'connection.php';
	$conn = createConnection();
	$WHERE="WHERE `level` BETWEEN '12' AND '14'";
	$lv=14;
	if (isset($_GET["B"])){
		$lv=$_GET["B"];
	}
	if (isset($_GET["S"])){
		$WHERE=" WHERE `level` BETWEEN ".$_GET["S"]." AND ".$_GET["B"];
	}
	
	$rule=0;//規則: 0-普規 1-日規 2-gomoku(未實作 也沒有對應的題庫)
	//empty()遇到0會回傳是空的
	if (isset($_GET["RULE"])){
		$rule=$_GET["RULE"];
	}
	$mode=0;//遊戲模式: 0-練習模式 1-盲解模式 2-比賽模式 3-綜合模式
	//empty()遇到0會回傳是空的
	if (isset($_GET["MODE"])){
		$mode=$_GET["MODE"];
	}
	$changeMode=1;//判斷是不是綜合模式(預設會自動切換)
	if($mode==3){
		$mode=2;
	}
	else{
		$changeMode=0;
	}
	$nikeName="Player";
	if (isset($_GET["NIKE"])){
		$nikeName=$_GET["NIKE"];
	}
 	
	//$w_color="rgb(220,188,250)";
	$w_color="#FFFFFF";
	$b_color="#000000";
	$bd_color="#D5BC57";
	$bg_color="#7AB4FA";
	$tb_color="#FFFF0A";
	$sc_color="#040038";

	//$sql = "SELECT * FROM `score`";
	$sql = "SELECT `no`,`puzzle`,`level` FROM `VC4` $WHERE ORDER BY `no`";
	$result = $conn->query($sql);

/*
	$nn=0;
	//$rr=$result->num_rows;
	//判斷SQL執行後是否有值出來 有的話執行相關動作	
	if ($result->num_rows > 0) {
		//$key = array_keys($row);
		// output data of each row
		while($row = $result->fetch_assoc()) {	
			$rr .= $row['puzzle'] ;  	     
    	}
  
  	} else {}
	*/
	$arr = array();
	$n=0;
		foreach($result as $row){
			$n=$n+1;
			$arr[$n] = array($row['no'],substr($row['puzzle'],6),$row['level']);
		}

	$conn->close();
?>

<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
<head>
    <style type="text/css">
	/*讓視窗不出現拉bar*/
	/* Firefox */
	html {
    	overflow: -moz-hidden-unscrollable;
    	height: 100%;
	}
	body {
    	-ms-overflow-style: none;/*IE */
    	height: 100%;
		width: calc(100vw + 18px);
		overflow: auto;
		-webkit-user-select:none;
		-moz-user-select:none;
		-o-user-select:none;
		user-select:none;
	}
	/* Chorme */
	body::-webkit-scrollbar {
    	display: none;
	}	
	/*讓視窗不出現拉bar*/

	/*棋盤外框格表現樣式 */
	.showBoard{
		background-color:<?php echo $bg_color?>; 
		width:100%;
		height:100%; 
		margin:0 auto ; 
		border:0 ; 
		padding:0 ;
	}	
	/*需要隱藏的項目加上這class名稱*/
	.eHide{
		display: none;
	}
	.readyGobox{
		height:100%;		
	
		position:relative;
		justify-content: center;	
		text-align:center;
	}
	.readyGo{
		
		position: relative;  
		/*text-align:center;*/
		margin:auto ; 
		top:35%;	
		width:320px;
		height:160px;	
		font-size:65px;
	}
    </style>
    <meta charset="utf-8" />
	<meta name="viewport" content="user-scalable=no">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<SCRIPT language=JavaScript1.1>
		//VVVVV禁用右鍵		
		var isNS = (navigator.appName == "Netscape") ? 1 : 0;
		var EnableRightClick = 0;
		if(isNS)
		document.captureEvents(Event.MOUSEDOWN||Event.MOUSEUP);
		function mischandler(){
		if(EnableRightClick==1){ return true; }
		else {return false; }
		}
		function mousehandlerU(e){
			if(EnableRightClick==1){ return true; }
			var myevent = (isNS) ? e : event;
			var eventbutton = (isNS) ? myevent.which : myevent.button;
			if(eventbutton==2){
				//中鍵2確認答案
				doCheckAns();				
				return false;	
			}
			if(eventbutton==3){
				//右鍵3退一步
				goBack1Step();
				return false;	
			}
		}
		function mousehandlerD(e){
			if(EnableRightClick==1){ return true; }
			var myevent = (isNS) ? e : event;
			var eventbutton = (isNS) ? myevent.which : myevent.button;
			if(eventbutton==2||eventbutton==3){
				return false;	
			}
		}
		function keyhandler(e) {
		var myevent = (isNS) ? e : window.event;
		if (myevent.keyCode==96)
		EnableRightClick = 1;
		return;
		}
		document.oncontextmenu = mischandler;
		document.onkeypress = keyhandler;
		document.onmousedown = mousehandlerD;
		document.onmouseup = mousehandlerU;
		//AAAAA禁用右鍵			
</SCRIPT>
</head>
<body id="outbox" style="width:100%;height:100%; margin:0 ; border:0; padding:0 ; text-align:center;" >
<div id="svgContainer" class="eHide" style=" background-color:<?php echo $bg_color?>; width:90%;height:90%; margin:0 auto ; border:0 auto; padding:0 auto;" hidden>

<svg id="board" width="100%" height="100%" viewBox="0 0 680 680" xmlns="http://www.w3.org/2000/svg" version="1.1" style="background-color:<?php echo $bd_color?>; margin: 0 auto; border: 0 auto; padding:0 auto;" >
<!-- style="background-color:#C4B45C" -->
<!-- width="680px" height="680px" -->
<!-- width="100vh" height="100vh" -->
<!--
<style>
.coord{ font: bold 10px serif; fill:black; }
.black{ font: bold 10px serif; fill:white; }
.white{ font: bold 10px serif; fill:black; }
.blacklast{ font: bold 10px serif; fill:white; }
.whitelast{ font: bold 10px serif; fill:black; }
.board{background-color: green;}
</style>-->
<!-- 最上層似乎是容器外框 -->
<defs ><radialGradient id="1r_0.75_0.75__fff-_A0A0A0" fx="0.75" fy="0.75"><stop offset="0%" stop-color="#ffffff"> </stop><stop offset="100%" stop-color="#a0a0a0" stop-opacity="1"> </stop></radialGradient><radialGradient id = "1r_0.75_0.75__A0A0A0-_000" fx="0.75" fy="0.75"><stop offset="0%" stop-color="#a0a0a0"> </stop><stop offset="100%" stop-color="#000000" stop-opacity="0.9"> </stop></radialGradient><clipPath id="clipPath4" ><rect  x ="5" y="5" width="680" height="680" /></clipPath></defs><g style="clip-path: url(#clipPath4);">	<polygon points="60,60 620,60 620,620 60,620 " fill="transparent" stroke-width="2" stroke="rgb(0,0,0)" />
	<!-- 橫直棋盤格線 -->
	<line x1="60" y1="100" x2="620" y2="100" stroke-width="1" stroke="rgb(0,0,0)" />
	<line x1="100" y1="60" x2="100" y2="620" stroke-width="1" stroke="rgb(0,0,0)" />
	<line x1="60" y1="140" x2="620" y2="140" stroke-width="1" stroke="rgb(0,0,0)" />
	<line x1="140" y1="60" x2="140" y2="620" stroke-width="1" stroke="rgb(0,0,0)" />
	<line x1="60" y1="180" x2="620" y2="180" stroke-width="1" stroke="rgb(0,0,0)" />
	<line x1="180" y1="60" x2="180" y2="620" stroke-width="1" stroke="rgb(0,0,0)" />
	<line x1="60" y1="220" x2="620" y2="220" stroke-width="1" stroke="rgb(0,0,0)" />
	<line x1="220" y1="60" x2="220" y2="620" stroke-width="1" stroke="rgb(0,0,0)" />
	<line x1="60" y1="260" x2="620" y2="260" stroke-width="1" stroke="rgb(0,0,0)" />
	<line x1="260" y1="60" x2="260" y2="620" stroke-width="1" stroke="rgb(0,0,0)" />
	<line x1="60" y1="300" x2="620" y2="300" stroke-width="1" stroke="rgb(0,0,0)" />
	<line x1="300" y1="60" x2="300" y2="620" stroke-width="1" stroke="rgb(0,0,0)" />
	<line x1="60" y1="340" x2="620" y2="340" stroke-width="1" stroke="rgb(0,0,0)" />
	<line x1="340" y1="60" x2="340" y2="620" stroke-width="1" stroke="rgb(0,0,0)" />
	<line x1="60" y1="380" x2="620" y2="380" stroke-width="1" stroke="rgb(0,0,0)" />
	<line x1="380" y1="60" x2="380" y2="620" stroke-width="1" stroke="rgb(0,0,0)" />
	<line x1="60" y1="420" x2="620" y2="420" stroke-width="1" stroke="rgb(0,0,0)" />
	<line x1="420" y1="60" x2="420" y2="620" stroke-width="1" stroke="rgb(0,0,0)" />
	<line x1="60" y1="460" x2="620" y2="460" stroke-width="1" stroke="rgb(0,0,0)" />
	<line x1="460" y1="60" x2="460" y2="620" stroke-width="1" stroke="rgb(0,0,0)" />
	<line x1="60" y1="500" x2="620" y2="500" stroke-width="1" stroke="rgb(0,0,0)" />
	<line x1="500" y1="60" x2="500" y2="620" stroke-width="1" stroke="rgb(0,0,0)" />
	<line x1="60" y1="540" x2="620" y2="540" stroke-width="1" stroke="rgb(0,0,0)" />
	<line x1="540" y1="60" x2="540" y2="620" stroke-width="1" stroke="rgb(0,0,0)" />
	<line x1="60" y1="580" x2="620" y2="580" stroke-width="1" stroke="rgb(0,0,0)" />
	<line x1="580" y1="60" x2="580" y2="620" stroke-width="1" stroke="rgb(0,0,0)" />
	<!-- 橫直棋盤格線 -->
	
	<!-- 盤邊座標 -->
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="61" y="652" alignment-baseline="middle" text-anchor="middle" >A</text>
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="101" y="652" alignment-baseline="middle" text-anchor="middle" >B</text>
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="141" y="652" alignment-baseline="middle" text-anchor="middle" >C</text>
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="181" y="652" alignment-baseline="middle" text-anchor="middle" >D</text>
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="221" y="652" alignment-baseline="middle" text-anchor="middle" >E</text>
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="261" y="652" alignment-baseline="middle" text-anchor="middle" >F</text>
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="301" y="652" alignment-baseline="middle" text-anchor="middle" >G</text>
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="341" y="652" alignment-baseline="middle" text-anchor="middle" >H</text>
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="381" y="652" alignment-baseline="middle" text-anchor="middle" >I</text>
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="421" y="652" alignment-baseline="middle" text-anchor="middle" >J</text>
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="461" y="652" alignment-baseline="middle" text-anchor="middle" >K</text>
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="501" y="652" alignment-baseline="middle" text-anchor="middle" >L</text>
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="541" y="652" alignment-baseline="middle" text-anchor="middle" >M</text>
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="581" y="652" alignment-baseline="middle" text-anchor="middle" >N</text>
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="621" y="652" alignment-baseline="middle" text-anchor="middle" >O</text>
	<!--數字座標 -->
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="26" y="62" alignment-baseline="middle" text-anchor="middle" >15</text>
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="26" y="102" alignment-baseline="middle" text-anchor="middle" >14</text>
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="26" y="142" alignment-baseline="middle" text-anchor="middle" >13</text>
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="26" y="182" alignment-baseline="middle" text-anchor="middle" >12</text>
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="26" y="222" alignment-baseline="middle" text-anchor="middle" >11</text>
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="26" y="262" alignment-baseline="middle" text-anchor="middle" >10</text>
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="30" y="302" alignment-baseline="middle" text-anchor="middle" >9</text>
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="30" y="342" alignment-baseline="middle" text-anchor="middle" >8</text>
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="30" y="382" alignment-baseline="middle" text-anchor="middle" >7</text>
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="30" y="422" alignment-baseline="middle" text-anchor="middle" >6</text>
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="30" y="462" alignment-baseline="middle" text-anchor="middle" >5</text>
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="30" y="502" alignment-baseline="middle" text-anchor="middle" >4</text>
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="30" y="542" alignment-baseline="middle" text-anchor="middle" >3</text>
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="30" y="582" alignment-baseline="middle" text-anchor="middle" >2</text>
	<text fill="rgb(0,0,0)" font-size="24" font-family="Times New Roman" font-weight="bold" x="30" y="622" alignment-baseline="middle" text-anchor="middle" >1</text>
	<!--數字座標 -->

	<!-- 星位小點 -->
	<circle cx="180" cy="180" r="4" fill="rgb(0,0,0)" stroke-width="1" stroke="rgb(0,0,0)" />
	<circle cx="180" cy="500" r="4" fill="rgb(0,0,0)" stroke-width="1" stroke="rgb(0,0,0)" />
	<circle cx="500" cy="180" r="4" fill="rgb(0,0,0)" stroke-width="1" stroke="rgb(0,0,0)" />
	<circle cx="500" cy="500" r="4" fill="rgb(0,0,0)" stroke-width="1" stroke="rgb(0,0,0)" />
	<circle cx="340" cy="340" r="4" fill="rgb(0,0,0)" stroke-width="1" stroke="rgb(0,0,0)" />
	<!-- 星位小點 -->

</g></svg>

<div id="buttonArea">
<div id= "modeTitle">
	<svg id="timebar" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="100%" style="background-color:<?php echo $bg_color?>;" >
	<rect id="timebox" width="100%" height="100%" fill="<?php echo $tb_color?>"/>
	</svg></div>
<!--<input id="clickMe" type="button" value="clickme" onclick="doFunction();" />-->

<form name="myform" action="vcfmenu.php" method="POST" target="_self">
<input id="changeMode" type="button" value="改變模式(比賽)" onclick="" style="display: none;"/></input>
<input id="refresh" type="button" value="重來" onclick="" /></input>
<input id="back1" type="button" value="退一步" onclick="" /></input>
<input id="checkA" type="button" value="核對答案" onclick="" /></input>
	<input type="hidden" name="RULE" value="<?php echo $rule ?>"></input>
    <input type="hidden" name="lv" value="<?php echo $lv-3 ?>"></input>
    <input type="hidden" name="MODE" value="
	<?php
		if($changeMode==1) echo 3;
		else echo $mode;
	?>
	"></input>
    <input type="hidden" name="NIKE" value="<?php echo $nikeName?>"></input>
    <input id="tomenu" type="submit" value="回到目錄"></input>
</form>
<input id="no" type="text" value="1" size="4" maxlength="8"  /></input>
<input id="tono" type="button" value="指定題號" onclick="" /></input>


<h1 id= "output"></h1>
<h1 id= "output2"></h1>
<h1 id= "output3"></h1>
<h1 id= "output4"></h1>
<h1 id= "output5">是否綜合模式<?php echo $changeMode . " mode:".$mode?></h1>
</div>
</div>
<div id="readyGobox" class="readyGobox" width="100%" height="100%" style="display:block;line-height:100vh;">
	<input id="readyGo" class="readyGo" type="button" value="Ready GO"  onclick= /></input>
</div>
<div id="scorebox" width="100%" height="100%" style="display:none;">
<iframe id="scoreif" src="vcfmenu.php" name="if_s" height="100%" width="100%" title="Iframe Example"></iframe>

</div>

</body>
<script type="text/javascript">
var today; 
var startTmis=0;//開始計時的date毫秒數
var bcolor="<?php echo $b_color?>";
var wcolor="<?php echo $w_color?>";
var bdcolor="<?php echo $bd_color?>";
var bgcolor="<?php echo $bg_color?>";
var tbcolor="<?php echo $tb_color?>";
var sccolor="<?php echo $sc_color?>";


var tbtime;
var lefttime=10300;//剩餘時間 抓date時間差 預設180300 測試改10300
var totaltime=10300;//總時限 預設180300 測試改10300
var alltimecount=0;//總時間加總 要拿來判斷閃爍用
var timebarStr="";//時間條上面要秀的訊息



var allpuzzles='<?php echo json_encode(array_values($arr)) ?>';
var GameMode =<?php echo $mode?>;//遊戲模式 0:練習模式 1:盲解模式 2:比賽模式
var autoMode=<?php echo $changeMode?>;//判斷是否要執行自動切換的模式 1:切換 0:固定
var ruleType=<?php echo $rule?>;//規則 0:普規長連也算勝 1:日規黑有禁 2:gomokut長連不算勝(還沒實作)
var nowBW =1;//現在這題是拿黑還是拿白來解 在讀題目時判斷

//紀錄發現幾個死四(連5點) 供遞迴判斷用 出現第一個連5點就自動補子 出現第2個連5點不要補子(盲解模式判勝)
var find4=0;
var dfind4=0;//紀錄防守方在防守過程中有沒有發生反4 
var alreadyloss=0;//紀錄防守方已經連五以後 就不能繼續點



//衝四自動補子判斷
//普規定義
//死四
var w5=["22222"];
var w4=["02222","22220","20222","22022","22202"];
var wopen4=["022220"];//活四
var b6=["111111"];
var b5=["11111"];
var b4=["01111","11110","10111","11011","11101"];
var bopen4=["011110"];//活四
//禁手需要再思考
var oneLineStr="";//從落子點前後各5子的範圍讀成一個字串供判斷

//普規定義
function freeRule(){	
	b4=["01111","11110","10111","11011","11101"];
	bopen4=["011110"];
}

//禁手連珠定義
function renjuRule(){
	b6=["111111"];
	b5=["0111110","0111112","011111x","2111110","2111112","211111x","x111110","x111112","x11111x"];
	//6跟5會先判斷 不成立才會判斷4 所以找4不用再考慮5或6的情況
	b4=["001111","201111","x01111","111100","111102","11110x","0101110","0101112","010111x","2101110","2101112","210111x","x101110","x101112","x10111x","0110110","0110112","011011x","2110110","2110112","211011x","x110110","x110112","x11011x","0111010","0111012","011101x","2111010","2111012","211101x","x111010","x111012","x11101x"];//要多檢查兩端都必須非1(0,2,或盤端)
	//禁手活四定義要多檢查兩端都必須非1(0,2,或盤端x)
	bopen4=["00111100","00111102","0011110x","20111100","20111102","2011110x","x0111100","x0111102","x011110x"];
}

var randIdx= new Array();//亂數序號

//玩家的落子手順(二維陣列) 每一格存座標[x.y]
//第一格空下來 搭配count 直接對應現在第幾手
var p1moveList = new Array();
//防守方的落子手順(二維陣列) 每一格存座標[x.y]
var p2moveList = new Array();
//第一格預存棋盤外的座標 供題目一開始讀進來時先判斷防守方盤面是否已經有死四 有的話存死四中1點的座標
p2moveList[0]=[ -1, -1];

//棋子資訊的二維陣列
var chessinfo = new Array(); //先宣告一維
var ansinfo = new Array(); //使用者解答時的答案紀錄 //先宣告一維

var outbox = document.getElementById("outbox"); 
var divbox = document.getElementById("svgContainer"); 
var svgbox = document.getElementById("board"); 	
var btbox = document.getElementById("buttonArea"); 	
var scorebox = document.getElementById("scorebox"); 
  
var count=0;
var qIndex=0;//目前解第幾題
var ansTimes=1;//本題解了幾次

changeRule(ruleType);
reSize();//進入頁面先按照視窗大小調整一次
//SQL字串切割以後 陣列第[0]筆資料存的是代表切成幾筆 所以過濾器把字串長度太短的也砍掉
//allpuzzles=allpuzzles.split("move=").filter(item => (item != '' && item.length>5));
//allpuzzles=json2array(allpuzzles);
//allpuzzles每一筆資料是 no,puzzle,level
allpuzzles = JSON.parse(allpuzzles, function(key, value) { 
    return value;
});
shuffle_cards();//亂數排列

iniBoard();
//timestart();
//putPuzzleToBoard(allpuzzles[getRandomInt(1,allpuzzles.length)]);
putPuzzleToBoard(allpuzzles[randIdx[qIndex]],false);
//putPuzzleToBoard(allpuzzles[570]);//測試指定題
//putPuzzleToBoard(allpuzzles[571]);//測試指定題
setGameModwButton(GameMode);
//改變規則 主要是有無禁手的切換 0:普規 1:日規連珠禁手
function changeRule(n){
	ruleType=n;
	if(ruleType==0)	freeRule();
	else if(ruleType==1) renjuRule();
}
function iniBoard(){
	//一維長度為i,i為變數，可以根據實際情況改變
	for(var i=0;i<15;i++){
		//宣告二維，每一個一維陣列裡面的一個元素都是一個陣列；
		chessinfo[i] = new Array();
		ansinfo[i] = new Array();
		for(var j=0;j<15;j++){ //一維陣列裡面每個元素陣列可以包含的數量p，p也是一個變數；
			chessinfo[i][j]=0; //這裡將變數初始化，我這邊統一初始化為空，後面在用所需的值覆蓋裡面的值
			ansinfo[i][j]=0;
		}
	}
}
//洗牌  
function shuffle_cards(){
	
    randIdx= new Array();
	for(var i =0 ;i< allpuzzles.length ; i++){
		randIdx[i]=i;    
    }
    //依序跟隨機一張牌交換
    for(var i =0 ;i< allpuzzles.length ; i++){			
        var j=getRandomInt(0, allpuzzles.length-1);
        while(j==i){
            j=getRandomInt(0, allpuzzles.length-1);
        }//避免自己跟自己換
        var temp = randIdx[j];
        randIdx[j] = randIdx[i];
        randIdx[i] =temp;     
    }
}
//開始按鈕
document.getElementById("readyGo").onclick = function () {
	timestart();
};
//開始計時
function timestart()
{
	divbox.setAttribute("class","showBoard");
	reSize();
	document.getElementById("readyGobox").setAttribute("style","display:none;");
	
	today=new Date(); 
	//讓timebar開始倒數縮短
	clearInterval(tbtime);
	tbtime = setInterval("tbTimer()",20); 
}

//清空所有棋盤資訊 並擺上下一題
function resetBoardByNo(no){
	clearBoardNum();
	clearBoard();	

	//putPuzzleToBoard(allpuzzles[2]);
	//目前先隨機挑一題秀出來 allpuzzles[0]這格存的是陣列長度 不要呼叫
	//putPuzzleToBoard(allpuzzles[getRandomInt(1,allpuzzles.length)]);
	var idx=Number(no);
	//超過範圍從頭開始
	if(idx>=allpuzzles.length)idx=idx%allpuzzles.length;
	putPuzzleToBoard(allpuzzles[idx],false);
	//putPuzzleToBoard(allpuzzles[1]);
}
//清空所有棋盤資訊 並擺上下一題
function resetBoard(qidx,timeoutcheck){
	clearBoardNum();
	clearBoard();	
	//putPuzzleToBoard(allpuzzles[2]);
	//目前先隨機挑一題秀出來 allpuzzles[0]這格存的是陣列長度 不要呼叫
	//putPuzzleToBoard(allpuzzles[getRandomInt(1,allpuzzles.length)]);
	var idx=qidx;
	//超過範圍從頭開始
	if(idx>=allpuzzles.length)idx=idx%allpuzzles.length;
	putPuzzleToBoard(allpuzzles[randIdx[idx]],timeoutcheck);
	//putPuzzleToBoard(allpuzzles[1]);
}

//清空棋盤資訊 相關變數也要歸零
function clearBoard(){
	for(var i=0;i<15;i++){	
		for(var j=0;j<15;j++){ 
			chessinfo[i][j]=0; 
			ansinfo[i][j]=0;
			removeChessXY(i,j);
			removeSignXY(i,j);
		}
	}
	count =0;
	find4=0;
	dfind4=0;//紀錄防守方在防守過程中有沒有發生反4
	alreadyloss=0;//紀錄已經輸掉的情況不能繼續點
	p1moveList = new Array();
	p2moveList = new Array();
	p2moveList[0]=[ -1, -1];
}
//清空手順資訊
function clearBoardNum(){
	for(var i=0;i<15;i++){	
		for(var j=0;j<15;j++){ 		
			removeChessNumXY(i,j);			
		}
	}
}
//判斷座標是否超過邊界
function ifOutside(x,y){
	if(x<0||x>=15) return true; 
	if(y<0||y>=15) return true; 
	return false;
}
//在棋盤一開始先檢查防守方是否已經有死四(第一手其實沒得選的意思) 有的話把含死四其中一子的座標存到p2moveList[0]
function bodStart4check(bw){
	for(var i=0;i<15;i++){	
		for(var j=0;j<15;j++){ 		
			if(ansinfo[i][j]==bw){
				if(have4check(i,j,0,0)>=1){
					p2moveList[0]=[ i, j];
					return;
				}
			}	
		}
	}
}
//判斷是否已經連五 
function have5check(x,y){
	var stoneCount=0;
	var bw=ansinfo[x][y];
	//這邊應該直接看二維陣列的方向即可
	//橫向
	stoneCount=	sameStoneCount(x+1,y,1,0,bw) + sameStoneCount(x-1,y,-1,0,bw);
	if(stoneCount==4)return true;
	if(ruleType==0&&stoneCount>=4)return true;//普規長連也算勝
	if(ruleType==1&&bw==2&&stoneCount>=4)return true;//日規白長連也算勝
	
	//垂直向
	stoneCount = sameStoneCount(x,y+1,0,1,bw) + sameStoneCount(x,y-1,0,-1,bw);	
	if(stoneCount==4)return true;
	if(ruleType==0&&stoneCount>=4)return true;//普規長連也算勝
	if(ruleType==1&&bw==2&&stoneCount>=4)return true;//日規白長連也算勝
	
	//左上右下
	stoneCount = sameStoneCount(x+1,y+1,1,1,bw)+sameStoneCount(x-1,y-1,-1,-1,bw);
	if(stoneCount==4)return true;
	if(ruleType==0&&stoneCount>=4)return true;
	if(ruleType==1&&bw==2&&stoneCount>=4)return true;
	//左下右上
	stoneCount = sameStoneCount(x+1,y-1,1,-1,bw)+sameStoneCount(x-1,y+1,-1,1,bw);
	if(stoneCount==4)return true;
	if(ruleType==0&&stoneCount>=4)return true;
	if(ruleType==1&&bw==2&&stoneCount>=4)return true;

	return false;
}
//判斷黑在某一個方向是否有活四 字串判斷法
function bhaveOp4checkStrByP(x,y,px,py){
	var chessLine="";
	chessLine=getLineStringStart(x,y,px,py,5);
	for(var i=0;i<bopen4.length;i++){
		if(chessLine.indexOf(bopen4[i])!=-1)return true;			
	}
	return false;
}
//判斷黑是否有活四 字串判斷法
function bhaveOp4checkStr(x,y){
	var chessLine="";
	//橫向
	chessLine=getLineStringStart(x,y,1,0,5);
	for(var i=0;i<bopen4.length;i++){
		if(chessLine.indexOf(bopen4[i])!=-1)return true;			
	}
	//垂直向
	chessLine=getLineStringStart(x,y,0,1,5);
	for(var i=0;i<bopen4.length;i++){
		if(chessLine.indexOf(bopen4[i])!=-1)return true;			
	}
	//左上右下
	chessLine=getLineStringStart(x,y,1,1,5);
	for(var i=0;i<bopen4.length;i++){
		if(chessLine.indexOf(bopen4[i])!=-1)return true;			
	}
	//左下右上
	chessLine=getLineStringStart(x,y,1,-1,5);
	for(var i=0;i<bopen4.length;i++){
		if(chessLine.indexOf(bopen4[i])!=-1)return true;			
	}
	return false;
}
//判斷黑是否連五 字串判斷法
function bhave5checkStr(x,y){
	var chessLine="";
	//橫向
	chessLine=getLineStringStart(x,y,1,0,5);
	for(var i=0;i<b5.length;i++){
		if(chessLine.indexOf(b5[i])!=-1)return true;			
	}
	//垂直向
	chessLine=getLineStringStart(x,y,0,1,5);
	for(var i=0;i<b5.length;i++){
		if(chessLine.indexOf(b5[i])!=-1)return true;			
	}
	//左上右下
	chessLine=getLineStringStart(x,y,1,1,5);
	for(var i=0;i<b5.length;i++){
		if(chessLine.indexOf(b5[i])!=-1)return true;			
	}
	//左下右上
	chessLine=getLineStringStart(x,y,1,-1,5);
	for(var i=0;i<b5.length;i++){
		if(chessLine.indexOf(b5[i])!=-1)return true;			
	}
	return false;
}
//判斷黑是否長連 字串判斷法
function bhave6checkStr(x,y){
	var chessLine="";
	//橫向
	chessLine=getLineStringStart(x,y,1,0,5);
	for(var i=0;i<b6.length;i++){
		if(chessLine.indexOf(b6[i])!=-1)return true;			
	}
	//垂直向
	chessLine=getLineStringStart(x,y,0,1,5);
	for(var i=0;i<b6.length;i++){
		if(chessLine.indexOf(b6[i])!=-1)return true;			
	}
	//左上右下
	chessLine=getLineStringStart(x,y,1,1,5);
	for(var i=0;i<b6.length;i++){
		if(chessLine.indexOf(b6[i])!=-1)return true;			
	}
	//左下右上
	chessLine=getLineStringStart(x,y,1,-1,5);
	for(var i=0;i<b6.length;i++){
		if(chessLine.indexOf(b6[i])!=-1)return true;			
	}
	return false;
}


//判斷最後落子點是否有形成死4或活4 產生幾個連5點 autoDF判斷衝四是否要自動補子
//autoDF 0:不動作 1:衝四自動補子 2:己方連5
//move 現在是落子的第幾手
function have4check(x,y,autoDF,move){
	if(ifOutside(x,y)) return 0;
	var have4Count=0;
	var bw=ansinfo[x][y];
	//8個方向往外找
	var p;//為了讓防守活四不要固定擋在同一邊 所以正反向隨機改變先後去做
	if(getRandomInt(0, 99)%2==0)p=1;
	else p=-1;
	//橫向
	have4Count+=firstFind0stoneAndCheckIs4(x+p,y,p,0,bw,autoDF,move)+firstFind0stoneAndCheckIs4(x-p,y,-p,0,bw,autoDF,move);
	if(have4Count>=3)return have4Count;

	//垂直向
	have4Count+=firstFind0stoneAndCheckIs4(x,y+p,0,p,bw,autoDF,move)+firstFind0stoneAndCheckIs4(x,y-p,0,-p,bw,autoDF,move);
	if(have4Count>=3)return have4Count;
		
	//左上右下
	have4Count+=firstFind0stoneAndCheckIs4(x+p,y+p,p,p,bw,autoDF,move)+firstFind0stoneAndCheckIs4(x-p,y-p,-p,-p,bw,autoDF,move);
	if(have4Count>=3)return have4Count;
	//左下右上
	have4Count+=firstFind0stoneAndCheckIs4(x+p,y-p,p,-p,bw,autoDF,move)+firstFind0stoneAndCheckIs4(x-p,y+p,-p,p,bw,autoDF,move);

	return have4Count;
}

//判斷目前方向有幾個連續同目標色的子
//參數 二維座標xy 找尋方向px(-1 0 1) py(-1 0 1) 搭配可以找1個點向外的8個方向 bw:要找的顏色
function sameStoneCount(x,y,px,py,bw){
	//結束條件 本身不是同色 或是超出邊界
	if(ifOutside(x,y)) return 0;
	if(ansinfo[x][y]!=bw)return 0;

	return 1+sameStoneCount(x+px,y+py,px,py,bw);
}

//從選定的方向往外找到第一個空點(途中都必須是同色) 判斷該空點是否是死四的連5點(防守方應該要擋的點)
//move:現在是落子的第幾手
function firstFind0stoneAndCheckIs4(x,y,px,py,bw,autoDF,move){
	//結束條件 本身超出邊界	
	if(ifOutside(x,y)) return 0;
	//結束條件 本身是空點 回傳有無死四的判斷結果
	if(ansinfo[x][y]==0){
		//前後兩個方向統計同色其是否滿4個 如果是則是死四
		var stoneCount=sameStoneCount(x+px,y+py,px,py,bw)+sameStoneCount(x-px,y-py,0-px,0-py,bw);
		if((ruleType==1&&bw==1&&stoneCount==4)||(ruleType==0&&stoneCount>=4)||(bw==2&&stoneCount>=4))
		{	
			if(autoDF==1){
				//如果是玩家衝四 電腦自動補子
				find4++;
				//發現第一個死四(連5點) 自動補子檔住
				if(find4==1){
					var defbw;
					if(bw==1)defbw=2;
					else defbw=1;
					ansinfo[x][y]=defbw;
					//衝四自動補子
					if(GameMode==0)oneStepBW(x,y,defbw);
					p2moveList[move]=[ x, y];				
				}
			}
			else if(autoDF==2){
				//防守方檢查自己有無四 有的話連五 
				dfind4++;
				//發現第一個死四(連5點) 自動補子連五
				if(dfind4==1){
					var defbw;
					if(bw==1)defbw=2; 
					else defbw=1;
					ansinfo[x][y]=bw; //這邊已經是以防守方的顏色去找了 所以直接填入該棋色				
					if(GameMode==0){
						oneStepBW(x,y,bw);
						//貼上數字
						pNumOnBd(x,y,5,"#DD0000","r"+x+"_"+y);
					}
					p2moveList[move]=[ x, y];					
				}
			}
			else{
				//畫面不需要動作的盲解模式

			}
			
			return 1;
		}
		else return 0;		
	}
	//不同色
	else if(ansinfo[x][y]!=bw)return 0;
	//同色 找下一個點
	else return firstFind0stoneAndCheckIs4(x+px,y+py,px,py,bw,autoDF,move);
}


//從一個點依指定方向前後各讀len個點 存成字串供判斷 讀到邊界外存為"x"
//參數:座標x,y 指定方向px,py 正反向p(1or-1) 剩餘找幾個點
function getLineString(x,y,px,py,p,len){
	if(len<0)return "";//超過範圍就不讀
	//結束條件 邊界外1格當作x
	if(ifOutside(x,y)){
		//if(p==1)oneLineStr="x"+oneLineStr;//正向讀到的加到字串最前
		//else oneLineStr=oneLineStr+"x";//負向讀到的加到字串最尾
		return "x";
	} 
	if(p==1)return ""+getLineString(x+px,y+py,px,py,p,len-1)+""+ansinfo[x][y];//正向讀到的加到字串最前
	else return ""+ansinfo[x][y]+""+getLineString(x-px,y-py,px,py,p,len-1);//負向讀到的加到字串最尾

}
//從一個點依指定方向前後各讀len個點 存成字串供判斷 讀到邊界存為"x"
//每次執行前記得把oneLineStr清成空字串 
function getLineStringStart(x,y,px,py,len){
	return ""+getLineString(x+px,y+py,px,py,1,len-1)+""+ansinfo[x][y]+""+getLineString(x-px,y-py,px,py,-1,len-1);
	//直接回傳一個字串  正向點字串+起始點+負向點字串
}

//判斷從目前位置往外一個方向 遇到的第一個空點 補子後判斷有無 形成活四(有無活三的依據)
function firstFind0stoneAndCheckCanOP4(x,y,px,py,n,bw){
	//結束條件 超過活三的長度	
	if(n<0) return 0;
	//結束條件 本身超出邊界	
	if(ifOutside(x,y)) return 0;
	//結束條件 本身是空點 回傳能不能變活四的判斷結果
	if(ansinfo[x][y]==0){
		ansinfo[x][y]=bw;		
		var chessLine="";
		chessLine=getLineStringStart(x,y,px,py,5);
		if(bw==1){//黑棋的判斷
			for(var i=0;i<bopen4.length;i++){
				if(chessLine.indexOf(bopen4[i])!=-1){
					//判斷是否是禁點 //遞迴檢查時把連5也當禁
					if(forbiddenCheck(x,y)==false){
						ansinfo[x][y]=0;
						return 1;
					}
					else{
						ansinfo[x][y]=0;
						return 0;
					}
				}			
			}
		}
		else{//白棋的判斷
			for(var i=0;i<wopen4.length;i++){
				if(chessLine.indexOf(wopen4[i])!=-1){
					ansinfo[x][y]=0;
					return 1;					
				}			
			}
		}
		ansinfo[x][y]=0;
		return 0;
	}
	//不同色
	else if(ansinfo[x][y]!=bw)return 0;
	//同色 找下一個點
	else return firstFind0stoneAndCheckCanOP4(x+px,y+py,px,py,n-1,bw);
}


//統計判斷一個已經落子的點有產生幾個真的活三
function find3Can4sum(x,y){
	//分4個方向去找 一個方向只會有一個活三 存在可活四點回傳1  
	var sum3=0;
	var bw=ansinfo[x][y];
	//橫向
	if(firstFind0stoneAndCheckCanOP4(x+1,y,1,0,3,bw)+firstFind0stoneAndCheckCanOP4(x-1,y,-1,0,3,bw)>0)sum3++;
	//垂直向
	if(firstFind0stoneAndCheckCanOP4(x,y+1,0,1,3,bw)+firstFind0stoneAndCheckCanOP4(x,y-1,0,-1,3,bw)>0)sum3++;
	//左上右下
	if(firstFind0stoneAndCheckCanOP4(x+1,y+1,1,1,3,bw)+firstFind0stoneAndCheckCanOP4(x-1,y-1,-1,-1,3,bw)>0)sum3++;
	//左下右上
	if(firstFind0stoneAndCheckCanOP4(x-1,y+1,-1,1,3,bw)+firstFind0stoneAndCheckCanOP4(x+1,y-1,1,-1,3,bw)>0)sum3++;
	return sum3;
}
//判斷一個點是否是禁點(落子後判斷) 呼叫時先在該子補上黑棋再判斷 因為遞迴先落子判斷 所以連五也必須判斷(不是真的結束棋局)
function forbiddenCheck(x,y){
	if(ruleType!=1)return false;//非禁手模式沒禁手	
	if(ansinfo[x][y]!=1)return false;//只有黑棋才需要判斷禁點
	//有連五不是禁點 計算時當成禁點(因為下到這點棋局已結束 進行不下去) 另外判斷獲勝
	if(bhave5checkStr(x,y)==true){
		//if(can5win)return true;
		//else return false;
		return true;
	}
	//有長連是禁點
	if(bhave6checkStr(x,y)==true){
		document.getElementById("output").innerHTML = "長連禁";
		return true;
	}
	//雙四判斷
	var can5point=have4check(x,y,0,0);//計算有幾個連五點
	if(can5point>2)return true;//3個以上連5點必然是雙四 (活四+死四)
	if(can5point==2){
		//剛好2個連五點 判斷有無活四
		//有活四就不是四四禁 但有可能33禁 不能直接回傳無禁
		if(bhaveOp4checkStr(x,y)==false){
			document.getElementById("output").innerHTML = "雙四禁";
			return true;
		}
	}
	//雙三判斷 真的可以變成活四的三有幾個
	var numOf3=find3Can4sum(x,y);
	if(numOf3>=2){
		document.getElementById("output").innerHTML = "雙三禁";
		return true;
	}

	return false;
}
//檢查整個盤面的空點有那些是禁點 一開始讀進棋譜判斷一次 每次落子後也判斷一次
function reCheckForbidden(){
	if(ruleType!=1)return;//非禁手模式不動作
	if(GameMode!=0)return;//非練習模式不動作
	//隨著落子變化 有些原本是禁點 但被解禁的點 要把點上的X拿掉	
	for(var i=0;i<15;i++){		
		for(var j=0;j<15;j++){ 				
			if(ansinfo[i][j]==0){	
				ansinfo[i][j]=1;
				if(forbiddenCheck(i,j)==false)
				{
					//setOneMove(i,j,0);
					removeChess("f"+i+"_"+j);
				}else{
					if(bhave5checkStr(i,j)==true){
						//因為連五當成禁手來算 但最後卻算贏 所以不標示為禁點
						removeChess("f"+i+"_"+j);
					}
					else{
						//setOneMove(i,j,0);
						removeChess("f"+i+"_"+j);
						removeChess("r"+i+"_"+j);
						pSignOnBd(i,j,"X","#DD0000","f"+i+"_"+j);				
					}
				}			
				ansinfo[i][j]=0;
			}
		}
	}
}

function reSize(){	
	//document.body.offsetWidth
	//if(document.documentElement.clientWidth >=document.documentElement.clientHeight){	
	var sz=0;	
	if(document.body.offsetWidth >=document.body.offsetHeight){	
		sz=	document.body.offsetHeight;
		//divbox.setAttribute("style"," background-color:<?php echo $bg_color?>; width:"+sz*0.75+"px;height:"+sz*0.75+"px; margin:0 auto ; border:0 ; padding:0 ;");
		divbox.style.width=sz*0.75+"px";
		divbox.style.height=sz*0.75+"px";
	
		//svgbox.setAttribute("width","100%");
		//svgbox.setAttribute("height","100%");
	}
	else{
		sz=	document.body.offsetWidth;
		//divbox.setAttribute("style"," background-color:<?php echo $bg_color?>; width:"+sz+"px;height:"+sz+"px; margin:0 auto ; border:0 ; padding:0 ;");
		divbox.style.width=sz+"px";
		divbox.style.height=sz+"px";
		//svgbox.setAttribute("width","100%");
		//svgbox.setAttribute("height","100%");
	}
	//showScore();
	sz=Number.parseInt(divbox.style.width);

	document.getElementById('modeTitle').setAttribute("style","display:block;width:500px;height:30px;");
	document.getElementById('modeTitle').style.width=sz+'px';
	
	document.getElementById("refresh").setAttribute("style","width:"+sz*0.23+"px;height:"+sz*0.1+"px;");
	document.getElementById("back1").setAttribute("style","width:"+sz*0.23+"px;height:"+sz*0.1+"px;");
	document.getElementById("checkA").setAttribute("style","width:"+sz*0.23+"px;height:"+sz*0.1+"px;");
	document.getElementById("tomenu").setAttribute("style","width:"+sz*0.23+"px;height:"+sz*0.1+"px;");
	
	//避免網頁讀取時棋盤閃爍 所以棋盤一開始hidden 在resize後才秀出來
	document.getElementById("svgContainer").removeAttribute("hidden");
	setGameModwButton(GameMode);
}
window.onresize = function(){
	reSize();
}
//取亂數 
function getRandomInt(min, max) {
  min = Math.ceil(min);
  max = Math.floor(max);
  return Math.floor(Math.random() * (max - min) + min); //The maximum is exclusive and the minimum is inclusive
}
//把資料庫讀到的一個棋譜 存到棋盤的二維陣列上
function putPuzzleToBoard(pzl,timeoutcheck){
	//時間結束時跳頁 現在跳回主畫面 以後要跳到成績頁面
	if(timeoutcheck&&lefttime<=0){
		//document.myform.submit();
		showScore();
		return;
	}
	//var re = ;	
	clearBoard();
	//pzl[0]是該題目在資料庫的編號no, pzl[1]是題目手順, pzl[2]是level
	var moveArray = pzl[1].toUpperCase().split(/`|1|4/).filter(item => item != '');
	var str="";
	var wc=0;//統計黑白各幾子
	var bc=0;
	for(var i=0;i<moveArray.length;i++)
	{
		var bw;
		if(i%2==0){
			bw=1;//黑棋設為1
			bc++;
		}
		else {
			bw=2;//白棋設為2
			wc++;
		}
		var x= moveArray[i][0].charCodeAt(0)-'A'.charCodeAt(0);
		var y= moveArray[i][1].charCodeAt(0)-'A'.charCodeAt(0);
		y=14-y;
		ansinfo[x][y]= chessinfo[x][y]=bw;
		str+= x+","+y+"("+chessinfo[x][y]+")"+":";
		setOneMove(x,y,bw);
	}
	//黑白一樣多 輪黑下 否則輪白下
	if(bc==wc){
		nowBW=1;
		bodStart4check(2);//檢查防守方一開始是否已經死有四	
	}
	else {
		nowBW=2;
		bodStart4check(1);
	}
	shoWhosTurn();//判斷輪黑還是輪白解題
	reCheckForbidden();//練習模式 顯示禁手點
	
	//document.getElementById("output3").innerHTML = str; 		
	document.getElementById("output4").innerHTML = "本次題庫共"+allpuzzles.length+"題,目前亂數取第"+randIdx[qIndex]+"題"; 
}

//依標籤ID移除棋子
function removeChess(idname){	
	var delID ; 	
	while(delID = document.getElementById(idname)){		
		delID.parentElement.removeChild(delID);
		//delID = document.getElementById(idname);			
	}	
}
//依座標移除棋子
function removeChessXY(x,y){	
	var delID ="c"+x+"_"+y;	
	removeChess(delID);
}
//依座標移除提示符號
function removeSignXY(x,y){	
	var delID ="r"+x+"_"+y;	
	removeChess(delID);
	delID ="f"+x+"_"+y;	
	removeChess(delID);
}
//依座標移除數字
function removeChessNumXY(x,y){		
	var delID ="bd"+x+"_"+y;	
	removeChess(delID);
	delID ="t"+x+"_"+y;	
	removeChess(delID);	
}

//設定棋盤畫面上一個點          //黑1 白2 盤0 
function setOneMove(x,y,bwo){
	var chessStr="c"+x+"_"+y;
	if(bwo==0) { 
		//如果要設為空點就清空上面所有符號
		removeChess(chessStr);
		removeChessNumXY(x,y);
		removeSignXY(x,y);
		return;
	}	
	var circle=document.createElementNS("http://www.w3.org/2000/svg","circle");
	//棋子在棋盤上實際位置
	circle.setAttribute("cx",60+x*40);
	circle.setAttribute("cy",60+y*40);
	circle.setAttribute("r",18);//棋子半徑
	//棋子顏色
	if(bwo==1)circle.setAttribute("fill",bcolor);
	else if(bwo==2)circle.setAttribute("fill",wcolor);
	//棋子外框顏色
	circle.setAttribute("stroke-width",2);
	circle.setAttribute("stroke","rgb(0,0,0)");
	//棋子元件的ID(供將來刪除用)
	circle.setAttribute("id",chessStr);
	document.getElementById('board').appendChild(circle); 
}
//棋盤上一個點 畫上指定的數字 參數:xy陣列座標 要畫的數字 顏色 物件ID名稱
function pNumOnBd(x,y,moveNum,colorN,chessStr){
	var svgNS = "http://www.w3.org/2000/svg";
	var newText = document.createElementNS(svgNS,"text");
	//個位數文字大小
	if(moveNum<10){
		//數字文字在棋盤上實際位置
		newText.setAttributeNS(null,"x",50+x*40);     
		newText.setAttributeNS(null,"y",73+y*40); 
		//文字大小
		newText.setAttributeNS(null,"font-size","40");
	}
	//兩位數文字大小
	else if(moveNum<100){
		//數字文字在棋盤上實際位置
		newText.setAttributeNS(null,"x",43+x*40);     
		newText.setAttributeNS(null,"y",71+y*40); 
		//文字大小
		newText.setAttributeNS(null,"font-size","35");
	}
	//三位數文字大小
	else{
		//數字文字在棋盤上實際位置
		newText.setAttributeNS(null,"x",41+x*40);     
		newText.setAttributeNS(null,"y",68+y*40); 
		//文字大小
		newText.setAttributeNS(null,"font-size","26");
	}
	//數字文字顏色
	//newText.setAttribute("fill","#0099cc");
	newText.setAttribute("fill",colorN);
	//內文
	var textNode = document.createTextNode(moveNum);
	newText.appendChild(textNode);
	//數字元件的ID(供將來刪除用)
	newText.setAttribute("id",chessStr);

	document.getElementById("board").appendChild(newText);	
}
//棋盤上一個點 畫上指定的符號 參數:xy陣列座標 要畫的數字 顏色 物件ID名稱
function pSignOnBd(x,y,sgWord,colorN,chessStr){
	var svgNS = "http://www.w3.org/2000/svg";
	var newText = document.createElementNS(svgNS,"text");
	//個位數文字大小
	if(sgWord.length==1){
		//數字文字在棋盤上實際位置
		newText.setAttributeNS(null,"x",50+x*40);     
		newText.setAttributeNS(null,"y",72+y*40); 
		//文字大小
		newText.setAttributeNS(null,"font-size","32");
	}
	//兩位數文字大小
	else if(sgWord.length==2){
		//數字文字在棋盤上實際位置
		newText.setAttributeNS(null,"x",43+x*40);     
		newText.setAttributeNS(null,"y",71+y*40); 
		//文字大小
		newText.setAttributeNS(null,"font-size","32");
	}
	//三位數文字大小
	else{
		//數字文字在棋盤上實際位置
		newText.setAttributeNS(null,"x",41+x*40);     
		newText.setAttributeNS(null,"y",68+y*40); 
		//文字大小
		newText.setAttributeNS(null,"font-size","26");
	}
	//數字文字顏色
	//newText.setAttribute("fill","#0099cc");
	newText.setAttribute("fill",colorN);
	newText.setAttribute("font-family","sans-serif");
	//內文
	var textNode = document.createTextNode(sgWord);
	newText.appendChild(textNode);
	//數字元件的ID(供將來刪除用)
	newText.setAttribute("id",chessStr);

	document.getElementById("board").appendChild(newText);	
}


//設定棋盤畫面上一個點手順編號 N    
//GameMode解題模式 ,GameMode移到全域變數 因為有其他函數會用到
//0:練習模式(衝四自動補子) 
//1:盲解模式(讓你點手順 手順不消失) 
//2:比賽模式(只保留最後一點手順 其他手順都隱藏)
function setOneMoveNum(x,y,moveNum){
	//已經輸掉就不能再動作
	if(alreadyloss>0){
			count--;
			return;
	}	
	//超出邊界不動作
	if(ifOutside(x,y)){
			count--;
			return;
	}	
	var chessStr="t"+x+"_"+y;
	if(GameMode==2){
		//2:比賽模式(只保留最後一點手順 其他手順都隱藏)
		//有棋子的點不動作(編號重複代表使用者記錯 但還是讓他點)
		if(chessinfo[x][y]!=0){
			count--;
			return;
		}	
		//只有重複點到目前這手(有顯示數字)不動作 其他只要重複點就重計算
		if(document.getElementById(chessStr)){
			count--;
			return;
		}			
		clearBoardNum();//清空手順資訊	
		p1moveList[count]=[x,y];
		//ansinfo[x][y]=moveNum;			
	}
	else if(GameMode==1){
		//1:盲解模式(讓你點手順 手順不消失) 
		//有棋子或是已經有編號的點不動作
		if(chessinfo[x][y]!=0){
			count--;
			return;
		}
		//點到已經有手順數字的點不動作 
		if(document.getElementById(chessStr)){
			count--;
			return;
		}	
		p1moveList[count]=[x,y];
		//ansinfo[x][y]=moveNum;	
	}
	else {//0:練習模式(衝四自動補子)
		//練習模式以後要改為衝四自動補子(還要分禁手模式或普規模式)
		//有棋子或是已經有編號的點不動作
		if(chessinfo[x][y]!=0||ansinfo[x][y]!=0){
			count--;
			return;
		}			
		//落子
		p1moveList[count]=[x,y];
		ansinfo[x][y]=nowBW;
		oneStepBW(x,y,nowBW);

		//判斷落子結果
		//連5顯示
		if(have5check(x,y)){
			document.getElementById("output").innerHTML = "連五勝"; 

			//直接跳下一題
			qIndex++;		
			if(autoMode==1)GameMode=2;	
			resetBoard(qIndex,true);	
			ansTimes=1;		
			setGameModwButton(GameMode);
			return;
		}
		else{	
			if(ruleType==1){
				//日規時加上禁手判斷
				if(nowBW==1&&forbiddenCheck(x,y)==true){
					if(bhave5checkStr(x,y)==false){
						//因為連五當成禁手來算 但最後卻算贏 所以不列為禁點 要排除	
						setOneMove(x,y,0);//禁手不給下
						ansinfo[x][y]=0;
						count--;
						//pSignOnBd(x,y,"X","#DD0000","f"+x+"_"+y);
							
						//document.getElementById("output").innerHTML += "if有跑完"; 
						
						reCheckForbidden();
						return;
					}
				}
					//if(ruleType==1)reCheckForbidden();
				
			} 		
			//不是衝四不給下
			if(have4check(x,y,0,count)==0)
			{
				document.getElementById("output").innerHTML = "不是衝四1"; 
				setOneMove(x,y,0);//不是衝四不給下
				ansinfo[x][y]=0;
				count--;
				pSignOnBd(x,y,"?","#DD0000","r"+x+"_"+y);	
				return;
			}			
			else{
				
				dfind4=0;
				//防守方先判斷自己有無機會連5 有的話直接連5 而不是檔4
				//照理講是前一手的防守的位置去判斷防守方有無反四 //autoDF 0:不動作 1:衝四自動補子 2:己方連5
				var d4count=have4check(p2moveList[count-1][0],p2moveList[count-1][1],2,count);			
				if(d4count>=1){
					document.getElementById("output").innerHTML = "反連五!下錯了!"; 
					alreadyloss++;
				}
				else{
					//判斷這一手產生幾個4 //autoDF 0:不動作 1:衝四自動補子 2:己方連5
					var fourcount=have4check(x,y,1,count);
					if(ruleType==1&&nowBW==2&&fourcount>=1){
						//日規時加上禁手判斷
						var p2x=p2moveList[count][0];
						var p2y=p2moveList[count][1];
						if(forbiddenCheck(p2x,p2y)==true){
							if(bhave5checkStr(p2x,p2y)==false){
								document.getElementById("output").innerHTML = "逼禁勝"; 
								//直接跳下一題
								qIndex++;		
								if(autoMode==1)GameMode=2;	
								resetBoard(qIndex,true);	
								ansTimes=1;		
								setGameModwButton(GameMode);
								return;
							}
							else{
								document.getElementById("output").innerHTML = "反連五!下錯了!"; 
								alreadyloss++;
							}
						}	
					} 
					if(fourcount>=2){
						//在盲解挑戰模式要判斷為勝
						find4=0;
						document.getElementById("output").innerHTML = fourcount+"個連五點"; 
					}			
					else if(fourcount==1){
						find4=0;			
						document.getElementById("output").innerHTML = fourcount+"個連五點 擋住";
						

					}
					/*	
					else{								
						document.getElementById("output").innerHTML = "不是衝四2"; 
						setOneMove(x,y,0);//不是衝四不給下
						ansinfo[x][y]=0;
						count--;
						pSignOnBd(x,y,"?","#DD0000","r"+x+"_"+y);				
					}*/
				}
				
			}
		}	

	}
	reCheckForbidden();
	if(GameMode!=0){	
		//秀數字時先在後面貼上一個跟棋盤同色的圓
		oneNunBgStep(x,y);
		//貼上數字
		pNumOnBd(x,y,moveNum,"#0099cc",chessStr);
	}
}

//比賽模式跟盲解模式確認答案 基本上跟練習模式過程一樣 只是不畫圖
function checkAnswer()
{
	for(var i=1;i<=count;i++){
		var x=p1moveList[i][0];
		var y=p1moveList[i][1];

		//模擬練習模式(衝四自動補子)
		//點到有棋子的點代表點重複答錯了
		if(ansinfo[x][y]!=0){
			document.getElementById("output").innerHTML = "重複點"; 
			return false;
		}			
		//落子
		ansinfo[x][y]=nowBW;

		//判斷落子結果
		//連5顯示
		if(have5check(x,y)){
			document.getElementById("output").innerHTML = "連五勝"; 
			return true;
		}
		else{
			if(ruleType==1){
				//日規時加上禁手判斷
				if(nowBW==1&&forbiddenCheck(x,y)==true){
					if(bhave5checkStr(x,y)==false){				
						return false;
					}
				}				
			} 
			dfind4=0;
			//防守方先判斷自己有無機會連5 有的話直接連5 而不是檔4
			//照理講是前一手的防守的位置去判斷防守方有無反四 //autoDF 0:不動作 1:衝四自動補子 2:己方連5
			var d4count=have4check(p2moveList[i-1][0],p2moveList[i-1][1],2,i);
			if(d4count>=1){
				document.getElementById("output").innerHTML = "反連五!下錯了!1"; 
				document.getElementById("output3").innerHTML = "防守手順:"+p2moveList; 

				return false;
			}
			else{
				//判斷這一手產生幾個4 //autoDF 0:不動作 1:衝四自動補子 2:己方連5
				var fourcount=have4check(x,y,1,i);
				var p2x=p2moveList[i][0];
				var p2y=p2moveList[i][1];
				if(ruleType==1&&nowBW==2&&fourcount>=1){
					//日規時加上防守方禁手判斷					
					if(forbiddenCheck(p2x,p2y)==true){
						if(bhave5checkStr(p2x,p2y)==false){	
							document.getElementById("output").innerHTML = "逼禁勝"; 
							return true;
						}
						else{
							document.getElementById("output").innerHTML = "反連五!下錯了!2"; 							
							return false;
						}
					}	
				} 
				if(fourcount>=2){
					//在盲解挑戰模式要判斷為勝
					document.getElementById("output").innerHTML = fourcount+"個連五點"; 
					document.getElementById("output3").innerHTML = "防守手順:"+p2moveList; 

					return true;
				}			
				else if(fourcount==1){
					find4=0;			
					//代表這手是衝四沒錯 繼續檢查下一手
					//已知問題43變活四剛好能把反四反回去的時候 不會好判斷 還是活四當唯一條件比較好				
				}	
				else{								
					document.getElementById("output").innerHTML = "不是衝四"; 
					document.getElementById("output3").innerHTML = "防守手順:"+p2moveList; 

					return false;				
				}
			}
		}
	}
	return false;//檢查完答案還沒出現連5或活4 也是代表沒解對
}



var mytime ;
var positionCount=-1; //提示作答顏色的位置控制
var timep=1 ;
var dposition=-2 ;
var obj=document.getElementById("showBWturn");

//網頁一進來讓timebar開始倒數縮短
function tbTimer()
{	
	var now=new Date(); 
	//
	lefttime=totaltime-(now-today);
	//if(lefttime<0) lefttime=0;
	alltimecount+=20;
	var tb =document.getElementById("timebox");	
	var sz=Math.round((lefttime*10000)/totaltime)/100;
	if(sz<0)sz=0;
	sz=""+sz+"%";

	//剩餘時間比例轉成時間條長度
	tb.setAttributeNS(null,"width",sz); 	
	if(lefttime<10500){
		if(alltimecount%200==0){
			if(tb.getAttribute("fill")==tbcolor)tb.setAttribute("fill",bgcolor);
			else tb.setAttribute("fill",tbcolor);
		}
	}
	else if(lefttime<31000){
		if(alltimecount%500==0){
			if(tb.getAttribute("fill")==tbcolor)tb.setAttribute("fill",bgcolor);
			else tb.setAttribute("fill",tbcolor);
		}
	}
	var rmt=(Math.round(lefttime/10)/100);
	timebarWord(timebarStr+" "+rmt);
}

//提示顏色的棋子的縮放效果
function myTimer() {
	var r =obj.getAttribute("r");//讀到的r是字串 要轉成數字才能做+-
	r=Number.parseInt(r)+timep;
	if(r>35||r<20)timep=-timep;
	obj.setAttribute("r",r);
	
	if(positionCount==1){
		obj.setAttribute("cy",5);
		var pp =obj.getAttribute("cx");//讀到的r是字串 要轉成數字才能做+-
		pp=Number.parseInt(pp)+dposition;
		if(pp>=540){
			dposition=-2;
			obj.setAttribute("cx",0);
		}
		else if(pp<140)dposition=2;
		obj.setAttribute("cx",pp);		
	}
	else {
		obj.setAttribute("cx",675);
		var pp =obj.getAttribute("cy");//讀到的r是字串 要轉成數字才能做+-
		pp=Number.parseInt(pp)+dposition;
		if(pp>540)dposition=-2;
		else if(pp<140)dposition=2;
		obj.setAttribute("cy",pp);		
	}
	//tbTimer();
}
//提示玩家現在輪黑還是輪白
function shoWhosTurn(){	
	var chessStr="showBWturn";		
	positionCount=-positionCount;	
	if(!document.getElementById(chessStr)){
		circle=document.createElementNS("http://www.w3.org/2000/svg","circle");		
		/*
		if(positionCount==1){
			circle.setAttribute("cx",140);
			circle.setAttribute("cy",5);			
		}
		else if(positionCount==1){
			circle.setAttribute("cx",675);
			circle.setAttribute("cy",140);
		}*/
		circle.setAttribute("r",20);		
		circle.setAttribute("stroke-width",2);
		circle.setAttribute("stroke","rgb(0,0,0)");
		circle.setAttribute("id",chessStr);
		circle.setAttribute("style","display='block'");
		document.getElementById('board').appendChild(circle);		
	}
	clearInterval(mytime);
	var cc=document.getElementById(chessStr);
	if(nowBW==1){
		cc.setAttribute("fill",bcolor);			
		btbox.setAttribute("style","background-color:"+bcolor);	
		divbox.style.background=bcolor;
		divbox.style.color=wcolor;
		outbox.style.background=bcolor;
	}
	else {
		cc.setAttribute("fill",wcolor); 
		btbox.setAttribute("style","background-color:"+wcolor);	
		divbox.style.background=wcolor;
		divbox.style.color=bcolor;
		outbox.style.background=wcolor;
	}		
	if(positionCount==1){
		cc.setAttribute("cx",140);
		cc.setAttribute("cy",5);
	}
	else {
		cc.setAttribute("cx",675);
		cc.setAttribute("cy",140);
	}
	//dposition=-dposition;
	//document.getElementById("showBWturn").style.display="none";	
	mytime = setInterval("myTimer()",40); 
}


/*
var chess = document.getElementById("board");
chess.onclick=function(e){
			
			var i =(e.offsetX/30)|0;   //得到點選的x座標
			var j = (e.offsetY/30)|0;  //得到點選的y座標
			
			var x=i;
			var y=j;
		   
		    oneStep(x,y,true); 
		}*/
//這裡player true為玩家   false為電腦（下面會寫）
//黑1 白2 盤0
//打譜模式 同一點黑白空循環
function oneStep(x,y,player){
	var elem;		
	chessinfo[x][y]++;
	if(chessinfo[x][y]==3)chessinfo[x][y]=0;
	circle=document.createElementNS("http://www.w3.org/2000/svg","circle");
	circle.setAttribute("cx",60+x*40);
	circle.setAttribute("cy",60+y*40);
	circle.setAttribute("r",18);
	if(chessinfo[x][y]==1)circle.setAttribute("fill",bcolor);
	else if(chessinfo[x][y]==2)circle.setAttribute("fill",wcolor);
	circle.setAttribute("stroke-width",2);
	circle.setAttribute("stroke","rgb(0,0,0)");
	var chessStr="c"+x+"_"+y;
	circle.setAttribute("id",chessStr);
	if(chessinfo[x][y]!=0)
		document.getElementById('board').appendChild(circle);
	else if(chessinfo[x][y]==0) { 
		//var elements = document.getElementsByID("c7-7"); 

		//document.getElementById("c7-7").remove();
		removeChess(chessStr);
			//elem = document.getElementById(x+"_"+y);
			//elem.parentElement.removeChild(elem);
		//.removeChild(elements);
	}  
	//var str ="id="+ chessStr+" "+ count + " " + x + " " + y  +"chessinfo="+chessinfo[x][y] ;
	//document.getElementById("output").innerHTML += str; 
}
//在某點落子 直接指定顏色 黑1 白2 盤0
function oneStepBW(x,y,chessBW){
	var elem;	
	var chessStr="c"+x+"_"+y;
	circle=document.createElementNS("http://www.w3.org/2000/svg","circle");
	circle.setAttribute("cx",60+x*40);
	circle.setAttribute("cy",60+y*40);
	circle.setAttribute("r",18);
	if(chessBW==1)circle.setAttribute("fill",bcolor);
	else if(chessBW==2)circle.setAttribute("fill",wcolor);
	circle.setAttribute("stroke-width",2);
	circle.setAttribute("stroke","rgb(0,0,0)");
	circle.setAttribute("id",chessStr);
	if(chessBW!=0)
		document.getElementById('board').appendChild(circle);
	else if(chessBW==0) { 
	//var elements = document.getElementsByID("c7-7"); 

	  //document.getElementById("c7-7").remove();
	  removeChess(chessStr);
		//elem = document.getElementById(x+"_"+y);
 	 	//elem.parentElement.removeChild(elem);
	  //.removeChild(elements);
	}  
	//var str ="id="+ chessStr+" "+ count + " " + x + " " + y  +"chessinfo="+chessinfo[x][y] ;
	//document.getElementById("output").innerHTML += str; 
}

//顯示數字時 在背後畫一個跟棋盤同色的圓
function oneNunBgStep(x,y){
	circle=document.createElementNS("http://www.w3.org/2000/svg","circle");
	circle.setAttribute("cx",60+x*40);
	circle.setAttribute("cy",60+y*40);
	circle.setAttribute("r",16);
	circle.setAttribute("fill",bdcolor);
	var chessStr="bd"+x+"_"+y;
	circle.setAttribute("id",chessStr);
	document.getElementById('board').appendChild(circle);
}
		
document.getElementById('board').addEventListener('click', function (e) {
	count++;
	var p00= (divbox.offsetHeight*40)/680;
	var cSize=(divbox.clientHeight*40)/680;

	if(divbox.offsetHeight>=divbox.offsetWidth){
		p00= (divbox.offsetWidth*40)/680;
		cSize=(divbox.offsetWidth*40)/680;
	}
	//document.getElementById("output").innerHTML += " an "+p00+ " an "+cSize; 
	var i = ((e.offsetX-p00)/cSize) | 0;   //得到點選的x座標
	var j = ((e.offsetY-p00)/cSize) | 0;  //得到點選的y座標
			
	var x=i;
	var y=j;
	//oneStep(x,y,true);  
	//GameMode解題模式 0:練習模式 1:盲解模式 2:比賽模式
	//GameMode=0;
	
	setOneMoveNum(x,y,count);
	document.getElementById("output2").innerHTML = "玩家手順:"+p1moveList; 
	document.getElementById("output2").innerHTML = Math.round((lefttime*10000)/totaltime)/100;
	document.getElementById("output3").innerHTML = "防守手順:"+p2moveList; 

}, false);
//改變模式
document.getElementById("changeMode").onclick = function () {
	resetBoard(qIndex,false);
	GameMode++;
	if(GameMode>2)GameMode=0;
	setGameModwButton(GameMode);
};
function setGameModwButton(n){
	var rulestr="";
	var txtstr="";
	var titlestr="";
	var chessBW="";
	titlestr="本題第"+ansTimes+"次答題";
	if(n==2){
		txtstr="改變模式(比賽)";
		titlestr+="(比賽模式)";
	}
	else if(n==1){
		txtstr="改變模式(盲解)";
		titlestr+="(盲解模式)";
	}
	else {
		txtstr="改變模式(練習)";
		titlestr+="(練習模式)";
	}
	if(ruleType==0)rulestr="(普規無禁手 ";
	else if(ruleType==1) rulestr="(日規黑有禁 ";

	if(nowBW==1)chessBW="黑先勝Black's turn)";
	else if(nowBW==2) chessBW="白先勝White's turn)";
	

	document.getElementById("changeMode").setAttribute("value",txtstr);
	//document.getElementById("modeTitle").innerHTML = titlestr+rulestr+chessBW; 
	//document.getElementById("timebar").innerHTML = titlestr+rulestr+chessBW; 	
	timebarStr=titlestr+rulestr+chessBW;	
}
//時間條上的文字
function timebarWord(txtstr){
	removeChess("barWord");

	var svgNS = "http://www.w3.org/2000/svg";
	var newText = document.createElementNS(svgNS,"text");
	
	newText.setAttribute("id","barWord");	
	//文字大小
	newText.setAttributeNS(null,"font-size",parseInt(document.getElementById('modeTitle').style.width)/29);
	var fsz=newText.getAttributeNS(null,"font-size");
	fsz=28-((30-fsz)/2);
	//文字在bar上實際位置
	newText.setAttributeNS(null,"x",2);     
	newText.setAttributeNS(null,"y",fsz); 
	
	//數字文字顏色
	//newText.setAttribute("fill","#0099cc");
	var colorC = bcolor;
	//if(nowBW==1)colorC=wcolor;
	newText.setAttribute("fill",colorC);
	//內文
	var textNode = document.createTextNode(txtstr);
	newText.appendChild(textNode);
	//數字元件的ID(供將來刪除用)
	document.getElementById("timebar").appendChild(newText);	
}

//指定題號 輸入框按enter直接送
document.getElementById("no").onkeypress = function () {
	var no=document.getElementById("no");
	if (event.keyCode == 13){
		//enter
		if(no.value<0)no.value=allpuzzles.length-1;
		if(no.value>=allpuzzles.length)no.value=no.value%allpuzzles.length;
		
		resetBoardByNo(no.value);
		setGameModwButton(GameMode);
	}
};


//指定題號
document.getElementById("tono").onclick = function () {
	var no=document.getElementById("no");	
	if(no.value>=allpuzzles.length)no.value=no.value-allpuzzles.length;
	
	resetBoardByNo(no.value);
	setGameModwButton(GameMode);
};
//重來
document.getElementById("refresh").onclick = function () {
	if(count<=0)return;
	if(GameMode==0)ansTimes++;//練習模式只要一按重來就算重解一次 其他模式用核對當一次
	resetBoard(qIndex,false);
	setGameModwButton(GameMode);
};
//退一步
document.getElementById("back1").onclick = function () {
	goBack1Step();
};
//退一步
function goBack1Step(){
	if(count<=0)return;
	var p1x=p1moveList[count][0];
	var p1y=p1moveList[count][1];

	if(GameMode==2){
		//清除玩家上一步落子
		setOneMove(p1x,p1y,0);
		//ansinfo[p1x][p1y]=0;
		
		//要補上 上上一步的落子	
		//秀數字時先在後面貼上一個跟棋盤同色的圓
		//p1moveList[0]沒有值 要排除不然會出錯
		if(count-1>=1){
			var b1x=p1moveList[count-1][0];
			var b1y=p1moveList[count-1][1];
			
			oneNunBgStep(b1x,b1y);
			//貼上數字
			pNumOnBd(b1x,b1y,count-1,"#0099cc","t"+b1x+"_"+b1y);
		}
	}
	else if(GameMode==1){	
		//清除玩家上一步落子
		setOneMove(p1x,p1y,0);
		//ansinfo[p1x][p1y]=0;
		
	}
	else{	
		var p2x=p2moveList[count][0];
		var p2y=p2moveList[count][1];
		//清除玩家上一步落子
		setOneMove(p1x,p1y,0);
		ansinfo[p1x][p1y]=0;
		//清除上一步衝四自動補子
		setOneMove(p2x,p2y,0);
		ansinfo[p2x][p2y]=0;	
		reCheckForbidden();//禁手點檢查	
	}
	count--;
	alreadyloss=0;	
	document.getElementById("output").innerHTML ="退一手";
}
//核對答案
document.getElementById("checkA").onclick = function () {
	doCheckAns();
};
//核對答案
function doCheckAns(){
	document.getElementById("output5").innerHTML ="ccis"+count+"c";
	if(count<1)return;//還沒開始下 不需要動作
	if(GameMode==0){	
		var x=p1moveList[count][0];
		var y=p1moveList[count][1];	
		var p2x=p2moveList[count][0];
		var p2y=p2moveList[count][1];
		//if(document.getElementById("output").innerHTML =="2個連五點"){
		//活四或雙四對手會自動擋住一個 所以只要還有連5點代表獲勝
		if(alreadyloss==0&&have4check(x,y,0,count)>0){
		//答對跳下一題
		qIndex++;	
		if(autoMode==1)GameMode=2;		
		resetBoard(qIndex,true);	
		ansTimes=1;	
		setGameModwButton(GameMode);
		}
		return;//練習模式答對自動跳下一題 不需要動作
	}
	
	if(checkAnswer()==true){
		//答對跳下一題
		qIndex++;		
		if(autoMode==1)GameMode=2;	
		resetBoard(qIndex,true);		
		ansTimes=1;	
		setGameModwButton(GameMode);
	}
	else{		
		//答錯就降一個難度模式
		if(autoMode==1)GameMode--;
		resetBoard(qIndex,false);
		ansTimes++;	
		setGameModwButton(GameMode);	
	}	

}
//玩完以後秀出結算成績的畫面
function showScore(){
	var sif=document.getElementById("scoreif");
	//隱藏棋盤相關畫面
	divbox.setAttribute("style","display:none");		
	
	//秀iframe 開成績頁面(成績登入PHP)
	sif.setAttribute("frameborder","0");
	sif.setAttribute("scrolling","no");
	outbox.setAttribute("style","background-color:"+sccolor);
	scorebox.setAttribute("style","display:block");
	//document.myform.submit();

}

/*
//回到目錄
document.getElementById("menu").onclick = function () {
	if(count<=0)return;
	if(GameMode==0)ansTimes++;//練習模式只要一按重來就算重解一次 其他模式用核對當一次
	resetBoard(qIndex,true);
	setGameModwButton(GameMode);
};
*/





/*
document.getElementById('svgContainer').addEventListener('click', function (event) {
  document.getElementById("output").innerHTML = "Well, a container seems better.";  

}, true);
*/
</script>
