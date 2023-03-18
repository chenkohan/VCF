<?php
session_start();

$rule=0; //規則: 0-普規 1-日規 2-gomoku(未實作 也沒有對應的題庫)
$lv=0;	//題庫難度 依手數排
$mode=0;//遊戲模式: 0-練習模式 1-盲解模式 2-比賽模式 3-綜合模式
$nike="Player";

//本php頁面被呼叫的時候有沒有接收到傳入值
//
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //檢查 empty為內建函數判斷變數內容是不是空的    
	//isset 是否存在這變數
	if (isset($_POST["RULE"])) {  
        $_SESSION["RULE"]=$_POST["RULE"];
    } 
    if (isset($_POST["lv"])) {  
        $_SESSION["lv"]=$_POST["lv"];
    } 
	if (isset($_POST["MODE"])) {  
        $_SESSION["MODE"]=$_POST["MODE"];
    } 
	if (isset($_POST["NIKE"])) {  
		if($_POST["NIKE"]!=$nike) $_SESSION["NIKE"]=$_POST["NIKE"];
    } 
}

if (isset($_SESSION["RULE"])){
    $rule=$_SESSION["RULE"];
}
if (isset($_SESSION["NIKE"])){
    $nike=$_SESSION["NIKE"];
}
if (isset($_SESSION["MODE"])){
    $mode=$_SESSION["MODE"];
}
if (isset($_SESSION["lv"])){
    $lv=$_SESSION["lv"];
}


?>
<!DOCTYPE HTML>
<html>
<HEAD>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=450px, initial-scale=0.9">
<!--	<link rel=stylesheet href="puzzle.css">-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>	

</HEAD>
<body>
 

<h1 color='#ff8307'>衝四勝VCF詰棋挑戰</h1>
<form id="levelf">
<h2 color='#ff8307'>規則：</h2>
<select id="rule" size=1>
	<option value=0>普規(無禁手長連也算勝)</option>
	<option value=1>日規(黑有禁手,白可逼禁勝)</option>
</select>

<h2 color='#ff8307'>難度：</h2>
<select id="level" size=1></select>

<h2 color='#ff8307'>遊戲模式：</h2>
<select id="gameMode" size=1>
	<option value=0>練習模式(基礎)</option>
	<option value=1>盲解模式(挑戰)</option>
	<option value=2>比賽模式(高手)</option>
	<option value=3>綜合模式(鍛鍊)</option>
</select>
<h3 id="modeExp" color='#ff8307'>練習模式:每一手衝四都會直接在棋盤上落子,而且防守方會自動落子擋四(或先連五),當你成功連五會跳下一題 </h3>
	
<!--<input id="rank" type="button" value="Rank" onClick="location.href='prank.php'">-->

<div>Nickname ：<br>
	<input id="nikeName" type="text" name="comment" rows="1" cols="50" value="<?php echo $nike;?>"></input>  
</div>
</form>
<br><br>
<input id="set" type="button" value="Start game" >　




</body>
</html>

<script type="text/javascript">
window.history.replaceState(null, null, window.location.href);
var VC4MIN = 3;
var VC4MAX = 14;
var i;
var select="";
var rType=document.getElementById("rule");
var sel=document.getElementById("level");	
var mod=document.getElementById("gameMode");
for (i=VC4MIN ; i<=VC4MAX ; i++){
	sel.options[i-VC4MIN]=new Option(i, i);
}
sel.options.length=VC4MAX-VC4MIN+1;
sel.selectedIndex=<?php echo $lv ?>;
rType.selectedIndex=<?php echo $rule ?>;
mod.selectedIndex=<?php echo $mode ?>;
setExpByMode(<?php echo $mode ?>);

//select="<select id='select1'>"+select+"</select>"

//select="<from class='select'><font color='#ff8307'>Level ：</font><br/ >"+select+"</from>"
//$("#level").after(select,"<br class='select' / >","<br class='select' / >")

$(document).ready(function() {
 // $('#other1').change(function(){
 // 	console.log(isNaN($('#other1').val()))
 // 	if (isNaN($('#other1').val())){
 // 		$('#other1').val(1)
 // 		return
 // 	}
 // 	if ($('#other1').val()*1<=0){
 // 		$('#other1').val(1)
 // 		return
 // 	}


 // 	$('#other').val($('#other1').val())
 	 

 // })

 $('#set').click(function(){  	
	var S=Math.max($("#level").val()-2,3);
 	var B=$("#level").val();
	var M=$("#gameMode").val();
	var N=$("#nikeName").val();
	var R=$("#rule").val();
	/*
	HttpSession session=request.getSession()；
	session.setAttribute("lv",B)；
	session.setAttribute("mode",M)；
	session.setAttribute("nike",N)；
	$.ajax({type:'post',url:location.pathname+'?lv='+B+'&mode='+M+'&nike='+N ,
	success:function(result){}
	});
	*/

  	location.href='renju_board.php?type=VC4'+"&S="+S+"&B="+B+"&RULE="+R+"&MODE="+M+"&NIKE="+N; 
	//location.href='renju_board.php?type=';
 });

});

document.getElementById("gameMode").onchange = function () {
	var M=$("#gameMode").val();	
	setExpByMode(M);//依目前模式列出對應的說明
}
//依目前模式列出對應的說明
function setExpByMode(n){		
	var expTxt="";
	if(n==0){
		expTxt = "練習模式:每一手衝四都會直接在棋盤上落子,而且防守方會自動落子擋四(或先連五),當你成功連五會跳下一題<br><br>"; 
	}
	else if(n==1){
		expTxt = "盲解模式:每一手都會幫你標記數字手順並保留在棋盤上(不會有錯誤提示),<br>當你點到活四或雙四時,按下核對答案按鈕會幫你檢查,失敗重來,成功會跳下一題<br>(在按下核對按鈕前,不管按幾次重來跟退回上一步都算同一次作答)"; 
	}
	else if(n==2){
		expTxt = "比賽模式:棋盤上只會標示目前你所點的最後一子手順(不會有錯誤提示),<br>當你點到活四或雙四時,按下核對答案按鈕會幫你檢查,失敗重來,成功會跳下一題<br>(在按下核對按鈕前,不管按幾次重來跟退回上一步都算同一次作答)"; 
	}
	else if(n==3){
		expTxt = "綜合模式:每題第一次答題會採用比賽模式,答錯一次會改為盲解模式,<br>答錯兩次以上會變成練習模式,直到解對時才跳下一題,<br>這是最推薦的鍛鍊方式,您可以在過程中自行驗證思考的盲點"; 
			}
	document.getElementById("modeExp").innerHTML = expTxt;
}
</script>