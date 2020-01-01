var thisUrl=window.location.href;
var url=new URL(thisUrl);
var page_id=url.searchParams.get("id");

var API_URL = "https://nacoss-ful.000webhostapp.com/api/";

var example1=new Vue({
  el: '#post',
  data () {
    return {
      post: null,
      loading: true,
      errored: false
    }
  },
  methods: {
    getImage (imageURL) {
      return (imageURL == null) ? "assets/gallery/no-image.jpg" : API_URL+"../wordpress/wp-content/uploads/"+imageURL;
    }
  },
  mounted () {
    axios
      .get(API_URL + '?call=getpost&id='+page_id)
      .then(response => {
        this.post = response.data.data.body
        this.errored =  response.data.data.errorExist
      })
      .catch(error => {
        console.log(error)
        this.errored = true
      })
      .finally(() => this.loading = false)
  }
})