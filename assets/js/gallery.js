var example1=new Vue({
  el: '#gallery',
  data () {
    return {
      gallery: null,
      loading: true,
      errored: false
    }
  },
  // methods:{
  //   getImage (link) {
      

  //     link.match('<img src\s*=\s*\\*"(.+?)\\*"\s*/>')
  //         .match((img)=>{
  //           console.log(img)
  //         })

  // }

    

  // },
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
  }})