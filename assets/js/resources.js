var thisUrl=window.location.href;
var url=new URL(thisUrl);
var level=url.searchParams.get("level");

var example1=new Vue({
  el: '#resources',
  data () {
    return {
      resources: null,
      loading: true,
      errored: false
    }
  },

  methods: {
    formatResponse (resp) {
        //changing array to json
        var sortedCourses = {}

        for (var i = resp.length - 1; i >= 0; i--) { 
        	courseCode = resp[i].code;
        	if (resp[i].level == level || level == 'all') {
        		//if data matches course looked for
        		(sortedCourses[courseCode] === undefined) ? 
        			sortedCourses[courseCode] =  [resp[i]] : 
        			sortedCourses[courseCode].push(resp[i])  
        	}
        }

        this.resources = sortedCourses
    },

    getFileType(fileName) {
    	var fileTypes = {
    		"pdf" : '<i class="fa fa-file-pdf-o"></i>',
    		"docx" : '<i class="fa fa-file-word-o"></i>',
    		"doc" : '<i class="fa fa-file-word-o"></i>',
    		"png" : '<i class="fa fa-file-image-o"></i>',
    		"jpg" : '<i class="fa fa-file-image-o"></i>',
    		"pptx" : '<i class="fa fa-file-powerpoint-o"></i>',
    		"ppt" : '<i class="fa fa-file-powerpoint-o"></i>',
    		"csv" : '<i class="fa fa-file-excel-o"></i>',
    		"others" : '<i class="fa fa-file"></i>'
    	}

    	fileExt = fileName.split('.').pop();
    	return (fileTypes[fileExt] === undefined) ? fileTypes["others"] : fileTypes[fileExt]
    	// return '<i class="fa fa-file-pdf-o"></i>';
    }
},

  mounted () {
    axios
      .get(API_URL + '?call=getallfiles')
      .then(response => {
        this.errored =  response.data.data.errorExist
        this.formatResponse(response.data.data.body);
      })
      .catch(error => {
        console.log(error)
        this.errored = true
      })
      .finally(() => this.loading = false)
  }
