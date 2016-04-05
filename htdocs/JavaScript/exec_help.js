// 檢查欄位
function CheckFields()
{
	// 檢查『帳號』欄位
	var fieldvalue = document.getElementById("username").value;
	if (fieldvalue == "") 
	{
		alert("『帳號』欄位不可以是空白!");
		document.getElementById("username").focus();
		return false;
	}
	
	// 寄出電子郵件
	sendMail(document.getElementById("username").value);
	return true;
}
// 寄出電子郵件
function sendMail(username)
{
    var objUserData = new Object;
	objUserData.username = username;
	// 呼叫伺服端的mail_password.php, 在網址後加上使用者輸入的帳號
	var req = Spry.Utils.loadURL("GET","mail_password.php?username="+username, false, myCallBack, {userData: objUserData});
}
// 在收到伺服器的反應後，這個callback函數就會被觸發
function myCallBack(req) 
{
    // 查詢結果的記錄筆數 
    var count = req.xhRequest.responseText; 	
	// 使用者輸入的帳號存在於ch26資料庫的member資料表中
    if (count > 0) 
	{
		alert(req.userData.username + "\r\n您的密碼已經寄到您的電子信箱中"); 
		// 返回前一頁
		document.getElementById("return").value = "yes";
    }
	else
	{
		alert("您輸入的帳號 " + req.userData.username + " 不存在"); 
	}
}