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
	else if (fieldvalue.length < 3 || fieldvalue.length > 10) 
	{
		alert("『帳號』欄位的長度必須是3~10個字元!");
		document.getElementById("username").focus();
		return false;
	}
	else if (fieldvalue.search(/[\u4E00-\u9FA5]/g) != -1)
	{
		alert("『帳號』欄位不可以是中文!");
		document.getElementById("username").focus();
		return false;
	}
		
	// 檢查『密碼』欄位
	fieldvalue = document.getElementById("password").value;
	if (fieldvalue == "") 
	{
		alert("『密碼』欄位不可以是空白!");
		document.getElementById("password").focus();
		return false;
	}
	else if (fieldvalue.length < 6 || fieldvalue.length > 12) 
	{
		alert("『密碼』欄位的長度必須是6~12個字元!");
		document.getElementById("password").focus();
		return false;
	}
	else if (fieldvalue.search(/[\W]/g) != -1)
	{
		alert("『密碼』欄位必須是英文字母與數字!");
		document.getElementById("password").focus();
		return false;
	}
}