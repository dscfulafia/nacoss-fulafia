var API_URL = "https://nacoss-ful.000webhostapp.com/api/";
// var API_URL = "http://localhost/nacoss/api/";

var example1=new Vue({
  el: '#gallery',
  data () {
    return {
      gallery: null,
      loading: true,
      errored: false
    }
  },
  mounted () {
    axios
      .get(API_URL + '?call=getpage&page=gallery')
      .then(response => {
        this.gallery = response.data.data.body
        this.errored =  response.data.data.errorExist
      })
      .catch(error => {
        console.log(error)
        this.errored = true
      })
      .finally(() => this.loading = false)
  }
})