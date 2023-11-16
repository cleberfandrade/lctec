const getHours = () => {
    const clock = document.getElementsByClassName('clock')[0]
    const date = new Date()
    const hours = date.getHours()
    const minutes = date.getMinutes()
    const seconds = date.getSeconds()
    const hour = hours < 10 ? `0${hours}` : hours
    const minute = minutes < 10 ? `0${minutes}` : minutes
    const second = seconds < 10 ? `0${seconds}` : seconds
    clock.innerHTML = `${hour}:${minute}:${second}`
  }
  
 
  const getSaudacao = () => {
  let fraseDiv = document.getElementsByClassName('frase')[0]
  let frase = '';
  
  let data = new Date();
  let hora = data.getHours();

  if (hora >= 3 && hora < 12)
      frase = 'Bom dia';
  else if (hora >= 12 && hora < 18)
      frase = 'Boa tarde';
  else if (hora >= 18 && hora <= 23)
      frase = 'Boa noite';       
  fraseDiv.innerHTML = frase;

  }

  setInterval(() => {
    getHours()
    getSaudacao()
  }, 1000)


 