function calendar(mois){

  var date = new Date();
  var day = date.getDate();
  var month = date.getMonth();
  var year = date.getYear();

  if(year<=200)
  {
          year += 1900;
  }
  months = new Array('Janeiro', 'Fevereiro', 'Marco', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
  days_in_month = new Array(31,28,31,30,31,30,31,31,30,31,30,31);

  var moisaujorduiu = month;

  month = mois;

  //ano bissesto, muda dia fevereiro
  if(year%4 == 0 && year!=1900)
  {
          days_in_month[1]=29;
  }

  total = days_in_month[month]; //days month

  var date_today = day+' '+months[month]+' '+year;//22 ouctober 2014

  beg_j = date; //today date

  if(month > 0) { 
      soma = 0;
      for(var m=0; m<month; m++) {
          soma += days_in_month[m];
      }
      beg_j.setDate(soma+1);
  }
  else {
      beg_j.setDate(1);
  }


  if(beg_j.getDate()==2) //1
  {
          beg_j=setDate(0);
  }
  beg_j = beg_j.getDay();

  document.write('<table class="text-dark"><tr><th colspan="7">'+months[mois]+' '+year+'</th></tr>');
  document.write('<tr class="text-dark"><th>DOM</th><th>SEG</th><th>TER</th><th>QUA</th><th>QUI</th><th>SEX</th><th>SAB</th></tr><tr>'); 
  week = 0;

  for(i=1;i<=beg_j;i++)
  {
          var beforemonth = months[month-1]; 

         document.write('<td><div class ="divday" />'+(days_in_month[month-1]-beg_j+i)+'</div></td>');
          week++;
  }
  for(i=1;i<=total;i++)
  {
          if(week==0)
          {
              document.write("<tr>");
          }

          if(day==i && moisaujorduiu==month) //si le jour = le jour de aujordhui est si le mois = mois aujordui 
          {

              document.write("<td><b><div class ='divtoday' onclick='open_popup(\""+i+" "+months[month]+"\")' href='#'>"+i+"</div><b></td>"); //day of today
          }
          //les autre jours
          else
          {

              document.write("<td><div class ='divday' onclick='open_popup(\""+i+" "+months[month]+"\")' href='#'>"+i+"</div></td>");
          }
          week++;
          if(week==7)
          {
                  document.write('</tr>');
                  week=0;
          }
  }

      //pour les jour du prochain mois

       for(i=1;week!=0;i++)
      {
              var nextmonth = months[month+1];
              document.write('<td><div class ="divday">'+i+'</td>');
              week++;
              if(week==7)
              {
                      document.write('</tr>');
                      week=0;
              }
      }
  document.write('</table>');
}