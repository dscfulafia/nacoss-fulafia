var thisUrl=window.location.href;
var url=new URL(thisUrl);
var orderType=url.searchParams.get("order");
console.log(url.search)

switch (orderType) {
  case 'due':
    processDuePayment();  
    break;
  case 'shirt':
    processshirtPayment();  
    break;
  case 'jotter':
    processJotterPayment();  
    break;

  default:
  // window.location = 'store.html'
  // console.log('Youve reached default')    
    break;
}

function processDuePayment() {
  //for payment of dues
  // ?order=due&amount=1000&with_id_card=yes
  var amount = 0;//store amount
  var initialAmount = Number(url.searchParams.get('amount'));
  var idCardAmount = (url.searchParams.get('with_id_card') === 'yes') ? 500 : 0;

  amount = initialAmount + idCardAmount;
  (idCardAmount > 0) ? 
    updateForm(['Payment for NACOSS dues: 1000', 'NACOSS ID card: 500'], amount) :
    updateForm(['Payment for NACOSS dues: 1000'], amount);
}

function processshirtPayment() {
  // process payment for tshirt
  // ?amount=2000&order=shirt&quantity=2&size=L&back=TK
  var amount = 0;//store amount
  var initialAmount = Number(url.searchParams.get('amount'));
  var quantity = Number(url.searchParams.get('quantity'));
  var size = url.searchParams.get('size');
  var back = url.searchParams.get('back');

  amount = initialAmount * quantity;
  updateForm([quantity + ' NACOSS departmental T-shirt(s): ', 
            'Description: '+size+' WriteUp:'+ back], amount)
  
}

function processJotterPayment() {
  // process for jotter payment
  // ?amount=200&order=jotter&quantity=1&type=hard+cover
  var amount = 0;//store amount
  var initialAmount = (url.searchParams.get('type') == 'Plain')? 200 : 300;
  var quantity = Number(url.searchParams.get('quantity'));
  var type = url.searchParams.get('type');

  amount = initialAmount * quantity;
  updateForm([quantity + ' Jotter(s) '+ type], amount);

  
}


function updateForm(arrData, amount){
  //update form for creating invoice
  for (var i = arrData.length - 1; i >= 0; i--) {
    document.getElementById('orders').innerHTML += '<li>' + arrData[i] + '</li>' 
  }
  document.getElementById('amount').innerHTML = amount
  // console.log(arrData)
}

function processInvoice(data) {
  console.log('processing...')
  document.getElementById('invBtn').style.display = 'none'
  document.getElementById('status').innerHTML = '<i class="fa fa-refresh w3-spin"></i> Processing.....'
  return false
}