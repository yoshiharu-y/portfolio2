myDate = new Date(); 
 Year = myDate.getYear();
 if (Year<2000) 
 { 
   Year = 1900 + Year; 
 } 
 if (Year == 00) 
 { 
   Year = 2000
 } 


document.write("Copyright&nbsp;&copy;&nbsp;")
document.write(Year)
document.write("&nbsp;undefind.&nbsp;All&nbsp;rights&nbsp;reserved.")
