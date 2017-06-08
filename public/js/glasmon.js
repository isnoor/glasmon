/*=================================================
* Glasmon 1.0 (MHN addons)
* Created  May 2017
* isnoor.laksana@mail.ugm.ac.id
*==================================================*/

'use strict';
         var ButtonHomeInfoBox = React.createClass( {
            render : function() {
               return (<div className="col-md-3">
                        <div className={this.props.opened ==="true"? 'small-box bg-gray' : 'small-box bg-primary'} >
                           <div className="inner">
                              <h3>{this.props.count} </h3>
                              <p> {this.props.label} </p>
                           </div>
                           <div className="icon">
                              <i className={this.props.icon}></i>
                           </div>
                           <a  href={this.props.target} className="small-box-footer" onClick={this.props.onClick}>
                              Detail <i className="fa fa-arrow-circle-right"  ></i>
                           </a>
                        </div>
                     </div>);
            },

         });
   
         var ButtonHomeLineHorizontal = React.createClass( {
            render: function() {               
               return <a className={this.props.opened ==="true"?'btn btn-block btn-social bg-gray' : 'btn btn-block btn-social bg-primary'} onClick={this.props.onClick} >
                        <i className={this.props.icon}></i> {this.props.label} 
                     </a>;
            }
         });

         var HomeBundleChart = React.createClass( {
            getInitialState : function(){
               return {id: {
                     main : this.props.id+"chart",
                     monthly : this.props.id+ "monthly-chart",
                     monthlyTrigger : '#'+this.props.id+ "monthly-chart",
                     monthlyChart : this.props.id + "monthly-bar-chart",
                     monthlySelected : this.props.id+ "selected-monthly-chart",
                     realtime : this.props.id+ "realtime-chart",
                     realtimeTrigger : "#"+this.props.id+ "realtime-chart",
                     realtimeChart : this.props.id + "-realtime-line-chart",
                  },opened: 'monthly',
                  realtime:{
                     currentRequest :1,
                     currentResponse :0
                  },
                  barChartStartDate : new Date().getFullYear() +"-01-01 00:00:00",
                  barChartEndDate : (new Date().getFullYear() + 1)+"-01-01 00:00:00",
                  barChartPeriod : "monthly"
               };
            },
            tabRealtime: function(){
               $('#'+this.state.id.main+' .overlay').show();
               this.setState({opened:'realtime'});
               $("#"+this.state.id.monthly).hide();
               this.reloadRealtimeChart('api/home/line_chart', true);
               $('#'+this.state.id.main+' .overlay').hide();
            },
            tabMonthly: function(){ 
               this.setState({opened:'monthly'});
               $("#"+this.state.id.monthly).show();
            },
            componentDidMount:function() {
               this.reloadMontlyChart('api/home/bar_chart');
               let globalObj = this;
               $('#range_home_bar_chart').daterangepicker({
                  timePicker: true, 
                  timePickerIncrement: 15, 
                  timePicker24Hour: true,
                  "startDate": "01/01/"+new Date().getFullYear() +" 00:00:00",
                  "endDate": "01/01/"+ (new Date().getFullYear() + 1)+" 00:00:00",
                  timePickerSeconds:true,
                  format: 'MM/DD/YYYYY h:mm:ss'});
               
               $('#range_home_bar_chart').on('apply.daterangepicker',{globalObj :globalObj}, function(ev, picker) {
                  ev.data.globalObj.setState({
                     barChartStartDate : picker.startDate.format('YYYY-MM-DD HH:mm:ss'),
                     barChartEndDate : picker.endDate.format('YYYY-MM-DD HH:mm:ss'),
                  });
               });
                
               this.interval = setInterval(this.tick, 60000);
            },
            componentWillUnmount: function() {
               clearInterval(this.interval);
            },

            tick: function() {
               this.reloadRealtimeChart('api/home/line_chart');
            },
            eventMontlyChart:function(){
               this.reloadMontlyChart('api/home/bar_chart');
            }
            ,
            reloadMontlyChart: function(urlTarget){
               let state = this.state;
               let globalObj = this;
               $('#'+this.state.id.main+' .overlay').show();
               $('#'+this.state.id.main+' #'+this.state.id.monthly).hide();
               let period = $('#'+this.state.id.monthlySelected).val();
               $.ajax({
                  url : urlTarget+'/'+period,
                  type: "POST",
                  dataType: "json",
                  state : state, 
                  globalObj:globalObj,
                  data : "startDate="+state.barChartStartDate+"&endDate="+state.barChartEndDate,
                  success: function(data, textStatus, jqXHR){
                     $('#'+this.state.id.main+' #'+this.state.id.monthly).show();
                      let chart = this.state.barChart;
                        if(this.state.barChart !=null){
                           
                           chart.data.datasets[0].data =data.data.data;

                            chart.data.labels = data.data.labels;
                            chart.update();
                        }else{
                           var config = {
                             type: 'bar',
                             data: {
                               labels:data.data.labels,
                               datasets: [{
                                 label: "attacks",
                                 data: data.data.data,
                                 backgroundColor:'rgba(54, 162, 235, 0.2)',
                                 borderColor: 'rgba(54, 162, 235, 1)',
                               }]
                             },
                             options: {
                              responsive: true,
                              maintainAspectRatio: false,
                             }
                           };

                           var ctx = document.getElementById(this.state.id.monthlyChart).getContext("2d");
                           chart = new Chart(ctx, config);
                              
                        }
                     
                     this.globalObj.setState({barChart : chart});

                     $('#'+this.state.id.main+' .overlay').hide();
                  },
                  error: function (xhr) {
                     console.log('gagal');
                     $('#'+this.state.id.main+' #'+this.state.id.monthly).show();
                     $('#'+this.state.id.main+' .overlay').hide();
                  }
               });  
            },
            reloadRealtimeChart: function(urlTarget, load){
               if( (this.state.opened==='realtime' || load === true) && (this.state.realtime.currentRequest - 1) === this.state.realtime.currentResponse){
                  var currentResponse = this.state.realtime.currentResponse;
                  var currentRequest = this.state.realtime.currentRequest;
                  this.setState({realtime:{currentRequest:  currentRequest+ 1, currentResponse: currentResponse}});

                  let globalObj = this;
                  let  d = new Date();
                  $.ajax({
                     url : urlTarget,
                     type: "POST",
                     dataType: "json",
                     data : "timestamp="+d.toISOString(),
                     globalObj : globalObj, 
                     currentResponse : currentResponse,
                     currentRequest : currentRequest,
                     success: function(data, textStatus, jqXHR){
                        let chart = this.globalObj.state.realtimeChart;
                        if(this.globalObj.state.realtimeChart !=null){
                           
                           chart.data.datasets[0].data =data.data.data;

                            chart.data.labels = data.data.labels;
                            chart.update();
                        }else{
                           var config = {
                             type: 'line',
                             data: {
                               labels:data.data.labels,
                               datasets: [{
                                 label: "attacks",
                                 data: data.data.data,
                                 borderColor: 'rgba(54, 162, 235, 1)',
                                 fill: "boundary",
                                 lineTension: 0,
                                 bezierCurve: false
                               }]
                             },
                             options: {
                              responsive: true,
                              maintainAspectRatio: false,
                              scales: {
                                 xAxes: [{
                                   type: 'time',
                                   time: {
                                     displayFormats: {
                                       'millisecond': 'HH:mm:ss',
                                       'second': 'HH:mm:ss',
                                       'minute': 'HH:mm:ss',
                                       'hour': 'HH:mm:ss',
                                       'day': 'HH:mm:ss',
                                       'week': 'HH:mm:ss',
                                       'month': 'HH:mm:ss',
                                       'quarter': 'HH:mm:ss',
                                       'year': 'HH:mm:ss',
                                     }
                                   }
                                 }],
                               },
                             }
                           };

                           var ctx = document.getElementById(this.globalObj.state.id.realtimeChart).getContext("2d");
                           chart = new Chart(ctx, config);
                              
                        }
                        
                        this.globalObj.setState({realtime:{currentRequest:  this.currentRequest+1, currentResponse: this.currentResponse+1}, realtimeChart: chart});
                     },
                     error: function (xhr) {
                        console.log('gagal');
                        this.globalObj.setState({realtime:{currentRequest:  this.currentRequest-1, currentResponse: this.currentResponse}});
                     }
                  });
               }  
            },
            render: function() {               
               var style = {
                  tabChart:{
                     position: 'relative',
                     height:'100%'
                  },
                  chart:{
                     height: '100%',
                     width: '100% ',
                  },
                  barChart:{
                     height: '80%'
                  },
                  chartHeight:{
                     height:'300px'
                  }
               };
              
               return (
                  <div className="nav-tabs-custom" id={this.state.id.main}>
                     <div className="overlay">
                        <i className="fa fa-refresh fa-spin"></i>
                     </div>
                     <ul className="nav nav-tabs pull-right">
                        <li className="active">
                           <a href={this.state.id.monthlyTrigger} data-toggle="tab" onClick={this.tabMonthly}>Periodic</a>
                        </li>
                        <li>
                           <a href={this.state.id.realtimeTrigger} data-toggle="tab" onClick={this.tabRealtime}>Realtime</a>
                        </li>
                        <li className="pull-left header">
                           <i className="fa fa-bar-chart-o"></i>{this.state.label} Chart
                        </li>
                     </ul>
                     <div className="tab-content no-padding" style={style.chartHeight}>
                       <div className="chart tab-pane active" id={this.state.id.monthly} style={style.tabChart}>
                           <div className="row">
                              <div className="col-md-7 col-sm-12">
                                  <div className="input-group">
                                    <div className="input-group-addon">
                                      <i className="fa fa-clock-o"></i>
                                    </div>
                                    <input type="text" className="form-control pull-right" id="range_home_bar_chart"></input>
                                  </div>
                              </div>

                              <div className="col-md-4 col-sm-12">
                                  <select className="form-control" id={this.state.id.monthlySelected}>
                                   <option value="daily">Daily</option>
                                   <option value="weekly">Weekly</option>
                                   <option value="monthly">Monthly</option>
                                 </select>
                              </div>

                              <div className="col-md-1 col-sm-12">
                                  <button type="button" className="btn btn-info btn-flat" onClick={this.eventMontlyChart}>Show</button>
                              </div>
                           </div>
                           <div className="row" style={style.barChart}>
                              <div className="col-md-12" style={style.chart} >
                              <canvas className="chart" id={this.state.id.monthlyChart} style={style.chart}></canvas>
                              </div>
                           </div>
                        </div>

                        <div className="chart tab-pane" id={this.state.id.realtime} style={style.tabChart}>
                           <canvas className="chart" style={style.chart} id={this.state.id.realtimeChart}>line_chart</canvas>
                        </div>
                           
                     </div>
                   </div>
               );
            }
         });

   var DataTablesCustomize = React.createClass( {
      getInitialState: function(){
         if(this.props.shouldUpdate){
            var shouldUpdate = this.props.shouldUpdate;
         }else{
            var shouldUpdate = false;
         }
         return {
            id: this.props.id,
            header : this.props.header,
            url : this.props.url,
            shouldUpdate : shouldUpdate,
            optional : this.props.optional,
            isFirst : true
         }
      },
      componentDidMount: function(){
         let state = this.state;
         var globalObj = this;
         $('#'+this.state.id).DataTable({
            processing: true,
            searching : false,
            ordering: false,
            serverSide: true,
            ajax:{
               url : state.url,
               type: "POST",
               data: function ( d ) {
                  $.extend(d, globalObj.state.optional);
                  return d;
                  }
               }
            });
            
      },
      shouldComponentUpdate: function(nextProps, nextState){
         return this.state.shouldUpdate;
      },
      componentDidUpdate: function(){
         let state = this.state;
         var globalObj = this;
         if(this.state.isFirst){
            this.setState({isFirst:false});
         }else{
            $('#'+this.state.id).DataTable({
            processing: true,
            searching : false,
            ordering: false,
            serverSide: true,
            destroy:true,
            ajax:{
               url : state.url,
               type: "POST",
               data: function ( d ) {
                  $.extend(d, globalObj.state.optional);
                  return d;
                  }
               }
            });
         }            
      },
      componentWillReceiveProps: function(nextProps){
         this.setState({
            optional : nextProps.optional
         });
         /*this.forceUpdate();*/
      },
      changeFilter: function(event){
         var optional = this.state.optional;
         optional.value = event.target.value;
         this.setState(optional);
      },
      render: function(){
         var header = this.state.header.map(function(d, index){
            return <th key={d.toString()}>{d}</th>
         });
         var filter='';
         if(this.state.optional){
            if(this.state.optional.filterStatus ==='active'){
            var filters = this.props.optional.filters.map(function(d, index){
                     return <option key={d.toString()}>{d}</option>
                  });
            filter = <div className="form-group">
                  <label className="col-sm-2 control-label"> {this.state.optional.label} </label>
                  <div className="col-sm-10">
                     <select className="form-control" onChange={this.changeFilter}>
                        {filters}
                     </select>
                  </div>
                </div>;   
            }   
         }  

         return (
            <div className="row">
               <div className="col-md-12">
                  {filter}
               </div>
               <div className="col-md-12">
                  <div className="table-responsive">
                     <table  className="table table-bordered table-striped table-hover" id={this.state.id}>
                        <thead>
                           <tr>
                              {header}
                           </tr> 
                        </thead>
                        <tbody>
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
         ) ;
   }
   });

   var PieChartCustomize = React.createClass( {
      getInitialState: function(){
         return {
            id: this.props.id,
            url : this.props.url
         }
      },
      loadData : function(){
         var globalObj = this;
         $.ajax({
            url : globalObj.state.url,
            type: "POST",
            dataType: "json",
            globalObj : globalObj,
            success: function(data, textStatus, jqXHR){
               this.globalObj.setState({
                  labels : data.labels,
                  datasets : [data.datasets ]
               });
            },
            error: function (xhr) {
               console.log('gagal');
            }
         });  
      },
      componentWillMount : function(){
         this.loadData();
      },
      componentDidUpdate: function(){
         let state = this.state;

        
         var config = {
            type: 'doughnut',
            data: {
               labels:this.state.labels,
               datasets: this.state.datasets
               },
            options: {
               responsive: true,
               maintainAspectRatio: false,
               legend: {
                  display: true,
                  position: 'bottom',
                  labels: {
                      generateLabels: function(chart) {
                          var data = chart.data;
                          if (data.labels.length && data.datasets.length) {
                              var dataValue = data.datasets[0].data;
                              var total = dataValue.reduce(function (a, b) {return a + b;}, 0);
                              return data.labels.map(function(label, i) {
                                  var meta = chart.getDatasetMeta(0);
                                  var ds = data.datasets[0];
                                  var arc = meta.data[i];
                                  var custom = arc && arc.custom || {};
                                  var getValueAtIndexOrDefault = Chart.helpers.getValueAtIndexOrDefault;
                                  var arcOpts = chart.options.elements.arc;
                                  var fill = custom.backgroundColor ? custom.backgroundColor : getValueAtIndexOrDefault(ds.backgroundColor, i, arcOpts.backgroundColor);
                                  var stroke = custom.borderColor ? custom.borderColor : getValueAtIndexOrDefault(ds.borderColor, i, arcOpts.borderColor);
                                  var bw = custom.borderWidth ? custom.borderWidth : getValueAtIndexOrDefault(ds.borderWidth, i, arcOpts.borderWidth);
                                  var value = chart.config.data.datasets[arc._datasetIndex].data[arc._index];
                                  var percent = value / total *100 ; 
                                  percent = (Math.round(percent * 100)/100).toFixed(2);
                                  return {
                                      text: label + " : " + value + "("+percent+"%)",
                                      fillStyle: fill,
                                      strokeStyle: stroke,
                                      lineWidth: bw,
                                      hidden: isNaN(ds.data[i]) || meta.data[i].hidden,
                                      index: i
                                  };
                              });
                          } else {
                              return [];
                          }
                      }
                  }
              }
            }
         };
         var ctx = document.getElementById(this.state.id).getContext("2d");
         new Chart(ctx, config);
      },
      render: function(){
         var style = {
            chart:{
               height: '100%',
               width: '100% ',
            },
            container:{
               height:'300px'
            }
         };
         return (
            <div style={style.container}>
               <canvas className="chart" id={this.state.id} style={style.chart}></canvas>
            </div>
         ) ;
   }
   });

   var Sensor = React.createClass( {
      getInitialState: function(){
         return {
            id: "table-content-sensor",
            header : ["UUID","IP","COUNT"],
            url : "api/sensor/list"
         }
      },
      render : function() {
         return (
            <DataTablesCustomize  {...this.state} />
            );
      },
   });

   var Parameters = React.createClass( {
      getInitialState: function(){
         return {
            id: "table-content-parameter",
            header : ["No","Parameter","Pattern","Count"],
            url : "api/parameter/list",
            shouldUpdate : true,
            optional : {filterStatus : 'active',
               column:"pattern",
               value : "all",
               label : "Pattern ",
               filters:[]},
         }
      },
      loadData : function(){
         var globalObj = this;
         $.ajax({
            url : 'api/parameter/filter/list',
            type: "POST",
            dataType: "json",
            globalObj : globalObj,
            success: function(data, textStatus, jqXHR){
               if(data.result){
                  this.globalObj.setState({
                     id: "table-content-parameter",
                     header : ["No","Parameter","Pattern","Count"],
                     url : "api/parameter/list",
                     shouldUpdate : true,
                     optional : {filterStatus : 'active',
                        column:"pattern",
                        value : "all",
                        label : "Pattern ",
                        filters: data.data}
                  });
               }
            },
            error: function (xhr) {
               console.log('gagal');
            }
         });  
      },
      componentWillMount : function(){
         this.loadData();
      },
      render : function() {
         return (
            <DataTablesCustomize  {...this.state} />
            );
      }
   });

   var Pattern = React.createClass( {
      getInitialState: function(){
         return {
            pie : {
               id: "pattern-pie",
               url : "api/pattern/pie"   
            },
            tables : {
               id: "pattern-table-content",
               header : ["No","Pattern","Count" ],
               url : "api/pattern/list"
            }
         }
      },
      render : function() {
         return (
            < div className="row">
               <div className="col-md-offset-2 col-md-8">
                  <PieChartCustomize {...this.state.pie} />
               </div>
               <div className="col-md-12">
                  <hr />
               </div>
               <div className="col-md-12">
                  <DataTablesCustomize  {...this.state.tables} />
               </div>
            </div>
         );
      }
   });

   var Method = React.createClass( {
      getInitialState: function(){
         return {
            id: "method-pie",
            url : "api/method/pie"
         }
      },
      render : function() {
         return (
            <PieChartCustomize {...this.state} />
         );
      },
   });

   var Tools = React.createClass( {
      getInitialState: function(){
         return {
            pie : {
               id: "tools-pie",
               url : "api/tools/pie"   
            },
            tables : {
               id: "tools-table-content",
               header : ["No","Taksonomi","User Agent (Original)", "UUID_event" ],
               url : "api/tools/list",
               shouldUpdate : true,
               optional : {filterStatus : 'active',
                           column:"ua_browser",
                           value : "all",
                           label : "User Agent (Taksonomi)",
                           filters:[]},
            },
            tables1 :{
               id: "table-content-sensor",
               header : ["No","Taksonomi","Count","Percent"],
               url : "api/tools/taksonomi/list"
            }
         }
      },
      loadData : function(){
         var globalObj = this;
         $.ajax({
            url : 'api/tools/filter/list',
            type: "POST",
            dataType: "json",
            globalObj : globalObj,
            success: function(data, textStatus, jqXHR){
               if(data.result){
                  this.globalObj.setState({
                     tables : {
                        id: "tools-table-content",
                        header : ["No","Taksonomi","User Agent (Original)", "UUID_event" ],
                        url : "api/tools/list",
                        optional : {filterStatus :'active',
                                    column:"ua_browser",
                                    value : "all",
                                    label : "User Agent (Taksonomi)",
                                    filters: data.data}
                     }
                  });
               }
            },
            error: function (xhr) {
               console.log('gagal');
            }
         });  
      },
      componentWillMount : function(){
         this.loadData();
      },
      render : function() {
         return (
            < div className="row">
               <div className="col-md-offset-2 col-md-8">
                  <PieChartCustomize {...this.state.pie} />
               </div>
               <div className="col-md-12">
                  <hr />
               </div>
               <div className="col-md-12">
                  <div className="nav-tabs-custom">
                   <ul className="nav nav-tabs">
                     <li className="active"><a href="#ip_tab_1" data-toggle="tab">Taksonomi</a></li>
                     <li><a href="#ip_tab_2" data-toggle="tab">Original</a></li>
                   </ul>
                   <div className="tab-content">
                     <div className="tab-pane active" id="ip_tab_1">
                        <DataTablesCustomize  {...this.state.tables1} />
                     </div>
                     
                     <div className="tab-pane" id="ip_tab_2">
                        <DataTablesCustomize  {...this.state.tables} />
                     </div>
                     
                   </div>
                   
                 </div>
                  
               </div>
            </div>
         );
      },
   });

var datatable_state_live = '';

   var DataTablesIPaddress = React.createClass( {
      getInitialState: function(){
         if(this.props.shouldUpdate){
            var shouldUpdate = this.props.shouldUpdate;
         }else{
            var shouldUpdate = false;
         }
         return {
            id: this.props.id,
            header : this.props.header,
            url : this.props.url,
            shouldUpdate : shouldUpdate,
            optional : this.props.optional
         }
      },
      shouldComponentUpdate: function(nextProps, nextState){
         return this.state.shouldUpdate;
      },setReloadTable(){
          let state = this.state;
         var globalObj = this;
           datatable_state_live = $('#'+this.state.id).DataTable({
            processing: true,
            searching : false,
            ordering: false,
            serverSide: true,
            destroy:true,
            ajax:{
               url : state.url,
               type: "POST",
               data: function ( d ) {
                  $.extend(d, globalObj.state.optional);
                  return d;
                  }
               },
            columns: [
               {
                  "className":      'details-control',
                  "orderable":      false,
                  "data":          null,
                  "defaultContent": "<button type='button' class='btn btn-block btn-primary btn-sm'><i class='fa  fa-plus-square-o'></i></button>",
                  "width": 30
               },
               { "data": "no" },
               { "data": "country" },
               { "data": "ip" },
               { "data": "count" }
               ]
            });

            $('#'+this.state.id+' tbody').off('click').on('click', 'td.details-control', function () {
               var tr = $(this).closest('tr');
               console.log(tr);
               var row = datatable_state_live.row( tr );
          
                if ( row.child.isShown() ) {
               /* This row is already open - close it */
               row.child.hide();
               $(this).html("<button type='button' class='btn btn-block btn-primary btn-sm'><i class='fa  fa-plus-square-o'></i></button>")
               tr.removeClass('shown'); 
            }
            else {
               /* Open this row */
               let d = row.data();
               row.child( '<div  style="height: 300px;width: 100%; "><canvas className="chart" id="'+$(this).attr('id')+d.ip+'" style="height: 100%;width: 100%; "></canvas> </div>').show();
               $(this).html("<button type='button' class='btn btn-block btn-sm'><i class='fa  fa-minus-square-o'></i></button>")
               $.ajax({
                  url : "api/ip_address/line",
                  type: "POST",
                  dataType: "json",
                  data : "ip_address="+d.ip,
                  datatable_row : row,
                  id_target : $(this).attr('id')+d.ip, 
                  success: function(data, textStatus, jqXHR){
                     if (data.data.length == 1) {
                        this.datatable_row.child('<table class="table table-bordered table-condensed" style="padding-left:50px;">'+
                             '<tr>'+
                                 '<th>Data</th>'+
                                 '<td>'+data.data[0].x +'</td>'+
                             '</tr>'+
                             '<tr>'+
                                 '<th>Count</th>'+
                                 '<td>'+ data.data[0].y+'</td>'+
                             '</tr>'+
                         '</table>');
                     }else{
                        var config = {
                        type: 'line',
                        data: {
                           datasets: [{
                              label: "count",
                              data: data.data,
                              borderColor: 'rgba(54, 162, 235, 1)',
                              fill: "boundary",
                              lineTension: 0,
                              bezierCurve: false
                           }]
                        },
                        options: {
                           responsive: true,
                           maintainAspectRatio: false,
                           scales: {
                              xAxes: [{
                                 type: 'time',
                                 time: {
                                       format: 'YYYY-MM-DD HH:mm',
                                       tooltipFormat: 'll HH:mm'
                                   },
                                 scaleLabel: {
                                    display: true,
                                    labelString: 'Date'
                                 }
                                 }],
                              },
                           }
                        };

                        var ctx = document.getElementById(this.id_target).getContext("2d");
                        let chart = new Chart(ctx, config);          
                     }
                  },
                  error: function (xhr) {
                     console.log('gagal');
                  }
               });
               tr.addClass('shown');
            }
            } );
      },
      componentDidMount : function(){
         this.setReloadTable();
      },
      componentDidUpdate: function(){
         this.setReloadTable();
      },
      componentWillReceiveProps: function(){
         this.setState({
            optional : this.props.optional
         });
         this.forceUpdate();
      },
      changeFilter: function(event){
         var optional = this.state.optional;
         optional.value = event.target.value;
         this.setState(optional);
      },
      render: function(){
         var header = this.state.header.map(function(d, index){
            return <th key={d.toString()}>{d}</th>
         });
         var filter='';
         if(this.state.optional){
            if(this.state.optional.filterStatus ==='active'){
            var filters = this.props.optional.filters.map(function(d, index){
                     return <option key={d.toString()}>{d}</option>
                  });
            filter = <div className="form-group">
                  <label className="col-sm-2 control-label"> {this.state.optional.label} </label>
                  <div className="col-sm-10">
                     <select className="form-control" onChange={this.changeFilter}>
                        {filters}
                     </select>
                  </div>
                </div>;   
            }   
         }  

         return (
            <div className="row">
               <div className="col-md-12">
                  {filter}
               </div>
               <div className="col-md-12">
                  <div className="table-responsive">
                     <table  className="table table-bordered table-striped table-hover" id={this.state.id}>
                        <thead>
                           <tr>
                              {header}
                           </tr> 
                        </thead>
                        <tbody>
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
         ) ;
   }
   });

   var IPAddress = React.createClass( {
      getInitialState: function(){
         return {
            country:{
               pie : {
                  id: "country-pie",
                  url : "api/country/pie"   
               },
               tables : {
                  id: "country-table-content",
                  header : ["Detail","No","Country","IP Address","Count" ],
                  url : "api/country/list",
                  shouldUpdate : true,
                  optional : {filterStatus :'active',
                     column:"source_country",
                     value : "all",
                     label : "Country ",
                     filters: []}
                  }
               },
            IPAddress:{
              pie : {
                  id: "ip-address-pie",
                  url : "api/ip_address/pie"   
               },
            }
         }
      },
      loadData : function(){
         var globalObj = this;
         $.ajax({
            url : 'api/country/filter/list',
            type: "POST",
            dataType: "json",
            globalObj : globalObj,
            success: function(data, textStatus, jqXHR){
               if(data.result){
                  this.globalObj.setState({
                     country:{
                        pie : {
                           id: "country-pie",
                           url : "api/county/pie"   
                        },
                        tables : {
                           id: "country-table-content",
                           header : ["No","Country","IP Address","Count" ],
                           url : "api/country/list",
                           shouldUpdate : true,
                           optional : {filterStatus :'active',
                                       column:"source_country",
                                       value : "all",
                                       label : "Country ",
                                       filters: data.data}
                        }
                     }
                  });
               }
            },
            error: function (xhr) {
               console.log('gagal');
            }
         });  
      },
      componentWillMount : function(){
         this.loadData();
      },
      render : function() {
         return (
          <div className="row">
            <div className="col-md-12">
              
              <div className="nav-tabs-custom">
                <ul className="nav nav-tabs">
                  <li className="active"><a href="#ip_tab_1" data-toggle="tab">Country</a></li>
                  <li><a href="#ip_tab_2" data-toggle="tab">Uniq Ip</a></li>
                </ul>
                <div className="tab-content">
                  <div className="tab-pane active" id="ip_tab_1">
                     <div className="row">
                        <div className="col-md-offset-2 col-md-8">
                        <PieChartCustomize {...this.state.country.pie} />
                        </div>
                     </div>
                  </div>
                  
                  <div className="tab-pane" id="ip_tab_2">
                     <div className="row">
                        <div className="col-md-offset-2 col-md-8">
                        <PieChartCustomize {...this.state.IPAddress.pie} />
                        </div>
                     </div>
                  </div>
                  
                </div>
                
              </div>
            </div>
            <div className="col-md-12">
               <DataTablesIPaddress  {...this.state.country.tables} />
            </div>
          </div>
         );
      },
   });

   var PollingAttack = React.createClass( {
      getInitialState: function(){
         return {
                  dynamic1:{
                    new_attack_title:"0 New Attack",
                    new_attack : 0
                  },
                  showLoad : false
         }
      },
      componentDidMount:function() {
         this.interval = setInterval(this.tick, 60000);
      },
      componentWillUnmount: function() {
        clearInterval(this.interval);
      },
      tick: function() {
        this.doPolling('polling/new');
      },
      doPolling: function(urlTarget){
        if(this.state.showLoad === false){
          let globalObj = this;
          globalObj.setState({
            showLoad :true
          });
          $.ajax({
            url : urlTarget,
            type: "POST",
            dataType: "json",
            data : "timestamp="+globalObj.props.timestamp,
            globalObj : globalObj,
            success: function(data, textStatus, jqXHR){
              if(data.result && data.count > 0){
                this.globalObj.setState({
                  dynamic1:{
                    new_attack_title:data.count + " New Attack. Reload page, please!",
                    new_attack : data.count 
                  },
                  showLoad :false
                });
              }else{
                console.log('nothing new');
                this.globalObj.setState({
                  showLoad :false
                });
              }
            },
            error: function (xhr) {
              console.log('gagal');
              this.globalObj.setState({
                showLoad :false
              });
            }
            });  
        }
      },
      render : function() {
        let classMaster = '';
        if(this.state.showLoad === true){
          classMaster = "box-tools pull-right col-md-4 col-sm-6 col-xs-6";
        }else{
          classMaster = "box-tools pull-right";
        }
        let style = {progressbar: {width:'100%'}};
         return (
            <div className={classMaster}>
              {this.state.showLoad === true ? 
                  <div className="progress progress-sm active" data-toggle="tooltip" data-placement="bottom"  data-widget="chat-pane-toggle" data-original-title="Transformation data. Please wait for some moment.Dont reload or close this page.">
                  <div className="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style={style.progressbar}>
                    <span className="sr-only">   </span>
                  </div>
                </div> 
                :
              <button type="button" className="btn btn-box-tool bg-gray" data-toggle="tooltip" data-placement="right"  data-widget="chat-pane-toggle" data-original-title={this.state.dynamic1.new_attack_title}>
              <i className="fa fa-bomb"></i> <span className="badge bg-red-active disabled">{this.state.dynamic1.new_attack==0 ? '': this.state.dynamic1.new_attack}</span></button>
              }
            </div>
         );
      },
   });

function detailEvent(d){
   return '<table  class="table table-bordered table-condensed"  style="padding-left:50px;">'+
        '<tr>'+
            '<th>UUID_event</th>'+
            '<td>'+d.uuid_event+'</td>'+
        '</tr>'+
        '<tr>'+
            '<th>Hpfeed ID</th>'+
            '<td>'+d.hpfeed_id+'</td>'+
        '</tr>'+
        '<tr>'+
            '<th>Ident</th>'+
            '<td>'+d.ident+'</td>'+
        '</tr>'+
        '<tr>'+
            '<th>HTTP version</th>'+
            '<td>'+d.http_v+'</td>'+
        '</tr>'+
        '<tr>'+
            '<th>Tool</th>'+
            '<td>'+d.ua_browser+'</td>'+
        '</tr>'+
        '<tr>'+
            '<th>HTTP request</th>'+
            '<td>'+d.payload+'</td>'+
        '</tr>'+
    '</table>';
}

   var DataTablesEvent = React.createClass( {
      getInitialState: function(){
         if(this.props.shouldUpdate){
            var shouldUpdate = this.props.shouldUpdate;
         }else{
            var shouldUpdate = false;
         }
         return {
            id: this.props.id,
            header : this.props.header,
            url : this.props.url,
            shouldUpdate : shouldUpdate,
            optional : this.props.optional
         }
      },
      shouldComponentUpdate: function(nextProps, nextState){
         return this.state.shouldUpdate;
      },
      setReloadTable: function(){
         let state = this.state;
         var globalObj = this;
         datatable_state_live = $('#'+this.state.id).DataTable({
            processing: true,
            searching : false,
            ordering: false,
            serverSide: true,
            destroy:true,
            ajax:{
               url : state.url,
               type: "POST",
               data: function ( d ) {
                  $.extend(d, globalObj.state.optional);
                  return d;
                  }
               },
            columns: [
               {
                  "className":      'details-control',
                  "orderable":      false,
                  "data":          null,
                  "defaultContent": "<button type='button' class='btn btn-block btn-primary btn-sm'><i class='fa  fa-plus-square-o'></i></button>",
                  "width": 30
               },
               { "data": "no" },
               { "data": "timestamp" },
               { "data": "destination" },
               { "data": "source" },
               { "data": "method" },
               { "data": "parameter" },
               { "data": "pattern" }
               ]
         });

         $('#'+this.state.id+' tbody').off('click').on('click', 'td.details-control', function () {
               var tr = $(this).closest('tr');
               console.log(tr);
               var row = datatable_state_live.row( tr );
          
                if ( row.child.isShown() ) {
               /* This row is already open - close it */
               row.child.hide();
               $(this).html("<button type='button' class='btn btn-block btn-primary btn-sm'><i class='fa  fa-plus-square-o'></i></button>")
               tr.removeClass('shown'); 
            }
            else {
               /* Open this row */
               let d = row.data();
               row.child( detailEvent(d)).show();
               $(this).html("<button type='button' class='btn btn-block btn-sm'><i class='fa  fa-minus-square-o'></i></button>");
               tr.addClass('shown');
            }
            } );
      },componentDidMount: function(){
         this.setReloadTable();
      },
      componentDidUpdate: function(){
         this.setReloadTable();
      },
      componentWillReceiveProps: function(nextProps){
         this.setState({
            optional : nextProps.optional
         });
      },
      changeFilter: function(event){
         var optional = this.state.optional;
         optional.value = event.target.value;
         this.setState(optional);
      },
      render: function(){
         var header = this.state.header.map(function(d, index){
            return <th key={d.toString()}>{d}</th>
         });
         var filter='';
         if(this.state.optional){
            if(this.state.optional.filterStatus ==='active'){
            var filters = this.props.optional.filters.map(function(d, index){
                     return <option key={d.toString()}>{d}</option>
                  });
            filter = <div className="form-group">
                  <label className="col-sm-2 control-label"> {this.state.optional.label} </label>
                  <div className="col-sm-10">
                     <select className="form-control" onChange={this.changeFilter}>
                        {filters}
                     </select>
                  </div>
                </div>;   
            }   
         }  

         return (
            <div className="row">
               <div className="col-md-12">
                  {filter}
               </div>
               <div className="col-md-12">
                  <div className="table-responsive">
                     <table  className="table table-bordered table-striped table-hover" id={this.state.id}>
                        <thead>
                           <tr>
                              {header}
                           </tr> 
                        </thead>
                        <tbody>
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
         ) ;
   }
   });
   var DataEvent = React.createClass( {
      getInitialState: function(){
         return {
            id: this.props.id,
            idField : "event-column",
            idSearch : "event-search-value",
            header : this.props.header,
            url : this.props.url,
            shouldUpdate : true,
            optional : {filterStatus : 'non active',
               column:"none",
               value : "none",
               label : "Event searching",
               filters:[]}
         }
      },eventFilter: function(){
         this.setState({
            optional : {filterStatus : 'non active',
               column: $("#"+this.state.idField).val(),
               value : $("#"+this.state.idSearch).val(),
               label : "Event searching",
               filters:[]}
         });
      },
      render : function() {
         return (
            <div className="row">
               <div className="col-md-12 col-sm-12 col-xs-12">
               <div role="form">
                  <div className="col-md-4 col-sm-12">
                     <select className="form-control" id={this.state.idField}>
                        <option value="hpfeed_id">ID Hpfeed</option>
                        <option value="ident">Ident Sensor</option>
                        <option value="destination">IP Address of Sensor</option>
                        <option value="source.ip">IP Address of Attacker</option>
                        <option value="source.ua_browser">Attacker's tool</option>
                        <option value="method">Method</option>
                        <option value="http_v">HTTP version</option>
                     </select>
                  </div>

                  <div className="col-md-7 col-sm-12">
                     <input id={this.state.idSearch} className="form-control" placeholder="Identical searching (regex, case sensitive)..." type="text"></input>
                  </div>
                  <div className="col-md-1 col-sm-12">
                     <button type="button" className="btn btn-info btn-flat" onClick={this.eventFilter}>find</button>
                  </div>
               </div>
               </div>
               <br /><br />
               <div className="col-md-12 col-sm-12 col-xs-12">
                  <DataTablesEvent  {...this.state} />
               </div>
            </div>
            );
      },
   });

   var MainApp = React.createClass( {
            getInitialState : function(){
               return {static:{
                        sensor:{
                           label :'Sensor',
                           icon  : 'fa fa-bullseye',
                           opened : 'false'},
                        pattern:{
                           label :'Attack Pattern',
                           icon  : 'fa fa-bomb',
                           opened : 'false'},
                        parameter:{
                           label :'Parameters',
                           icon  : 'glyphicon glyphicon-pushpin',
                           opened : 'false'},
                        ip:{
                           label :'Uniq IP',
                           icon  : 'fa fa-map-marker',
                           opened : 'false'},
                        method:{
                           label :'Method',
                           icon  : 'fa fa-paw',
                           opened : 'false'},
                        tools:{
                           label :'User Agent(Tools)',
                           icon  : 'fa fa-internet-explorer',
                           opened : 'false'},
                        home:{
                           label :'Home',
                           icon  : 'fa fa-home',
                           opened : 'true'},
                        },
                     dynamicButton:{
                        sensor:{
                           opened : 'false'},
                        pattern:{
                           opened : 'false'},
                        parameter:{
                           opened : 'false'},
                        ip:{
                           opened : 'false'},
                        method:{
                           opened : 'false'},
                        tools:{
                           opened : 'false'},
                        home:{
                           opened : 'true'}
                        },
                     active:'home',
                     dynamic:{
                        sensor:{count:0},
                        pattern:{count:0},
                        parameter:{count:0},
                        attack:{
                           count:'0',
                           countThisMonth:'0',
                           last: {
                              sensor : 'glastopf1',
                              pattern : 'SQL injection',
                              parameter : '--index.php'
                           }
                        }
                     },
                     defaultDetail : {
                        id: "table-content-sensor",
                        header : ["Detail","No","Time","IP Honeypot","IP Attacker", "Method","Parameter","Pattern"],
                        url : "api/home/event"
                     }
                  }
            },
            componentDidMount:function() {
               var data = this.getCountData('api/home');
            },
            getCountData: function(urlTarget){
               let dataResult;
               let globalObj = this;
               $.ajax({
                  url : urlTarget,
                  type: "POST",
                  dataType: "json",
                  globalObj : globalObj,
                  dataResult : dataResult,
                  success: function(data, textStatus, jqXHR){
                     if(data.result){
                        this.dataResult = data.data;
                        this.globalObj.setState({
                           dynamic:{
                              sensor:{count:data.data.glastopf_sensor},
                              pattern:{count:data.data.pattern},
                              parameter:{count:data.data.parameter},
                              attack:{
                                 count:data.data.attack,
                                 countThisMonth:data.data.attack_last_month,
                                 last: {
                                    sensor : data.data.event_last.destination,
                                    pattern : data.data.event_last.pattern,
                                    parameter : data.data.event_last.parameter,
                                    timestamp : data.data.event_last.timestamp
                                 }
                              }
                           }
                        });
                     }else{
                        console.log('failed load data');
                     }
                  },
                  error: function (xhr) {
                     console.log('gagal');
                  }
               });  

               return dataResult;
            },
            handleSelectedDetail: function(identifier, event) {
               let dynamicButton = {dynamicButton:{
                                                sensor:{
                                                   opened : 'false'},
                                                pattern:{
                                                   opened : 'false'},
                                                parameter:{
                                                   opened : 'false'},
                                                ip:{
                                                   opened : 'false'},
                                                method:{
                                                   opened : 'false'},
                                                tools:{
                                                   opened : 'false'},
                                                home:{
                                                   opened : 'false'}
                                                }};
               switch(identifier) {
                  case 'ip':
                     dynamicButton.dynamicButton.ip.opened = 'true';
                     this.setState({active : 'ip'});
                     break;
                  case 'method':
                     dynamicButton.dynamicButton.method.opened = 'true';
                     this.setState({active : 'method'});
                     break;
                  case 'tools':
                     dynamicButton.dynamicButton.tools.opened = 'true';
                     this.setState({active : 'tools'});
                     break;
                  case 'sensor':
                     dynamicButton.dynamicButton.sensor.opened = 'true';
                     this.setState({active : 'sensor'});
                     break;
                  case 'pattern':
                     dynamicButton.dynamicButton.pattern.opened = 'true';
                     this.setState({active : 'pattern'});
                     break;
                  case 'parameter':
                     dynamicButton.dynamicButton.parameter.opened = 'true';
                     this.setState({active : 'parameter'});
                     break;
                  default:
                     dynamicButton.dynamicButton.home.opened = 'true';
                     this.setState({active : 'home'});
                     break;
               } 
               this.setState(dynamicButton);
               $("#detail-content").focus();
            },
            render: function() {
               let detailActive = {label : "Detail", icon: "fa fa-dashboard"};
               let detailContent = "";
               switch(this.state.active){
                  case 'ip' :
                     detailActive.label = this.state.static.ip.label ; 
                     detailActive.icon = this.state.static.ip.icon ;
                     detailContent =  <IPAddress />;
                  break;
                  case 'sensor' :
                     detailActive.label = this.state.static.sensor.label ; 
                     detailActive.icon = this.state.static.sensor.icon ; 
                     detailContent =  <Sensor />;
                  break;
                  case 'pattern' :
                     detailActive.label = this.state.static.pattern.label ; 
                     detailActive.icon = this.state.static.pattern.icon ; 
                     detailContent =  <Pattern />;
                  break;
                  case 'parameter' :
                     detailActive.label = this.state.static.parameter.label ; 
                     detailActive.icon = this.state.static.parameter.icon ; 
                     detailContent =  <Parameters />;
                  break;
                  case 'tools' :
                     detailActive.label = this.state.static.tools.label ; 
                     detailActive.icon = this.state.static.tools.icon ; 
                     detailContent =  <Tools />;
                  break;
                  case 'method' :
                     detailActive.label = this.state.static.method.label ; 
                     detailActive.icon = this.state.static.method.icon ; 
                     detailContent =  <Method />;
                  break;
                  default:
                     detailContent = <DataEvent  {...this.state.defaultDetail} />;
                  break;
               }
               let pollingStatus = "";
               
               
               return(
                   <div>
                     <section className="content row">
                     <div className="col-md-offset-1 col-md-10  col-sm-12 col-xs-12">
                        <div className="box box-default">
                           <div className="box-header with-border">
                              <i className="fa fa-dashboard"> </i>
                              <h3 className="box-title">Count</h3>

                              <PollingAttack timestamp={this.state.dynamic.attack.last.timestamp} />
                           </div>

                           <div className="box-body bg-navy">
                              <div className="row">
                                 <div className="col-md-offset-1 col-md-10 col-sm-12 col-xs-12">
                                    <div className="info-box bg-navy">
                                       <span className="info-box-icon"><i className="fa fa-dashboard"></i></span>

                                       <div className="info-box-content">
                                          <span className="info-box-text">Total Attack</span>
                                          <span className="info-box-number font-big">{this.state.dynamic.attack.count}</span>
                                          <span className="progress-description">
                                             {this.state.dynamic.attack.countThisMonth} attacks in this month
                                          </span>
                                       </div>

                                    </div>
                                    <HomeBundleChart label="Attack" id="main-chart" />
                                 </div>
                              </div>
                              <hr />
                                 <div className="row ">
                                    <div className="col-md-3">
                                       <h4> {this.state.dynamic.attack.last.sensor}</h4>
                                    </div>
                                    <div className="col-md-3">
                                       <h4>{this.state.dynamic.attack.last.pattern}</h4>
                                    </div>
                                    <div className="col-md-3">
                                       <h4>{this.state.dynamic.attack.last.parameter}</h4>
                                    </div>
                                    <div className="col-md-3">
                                       <h3 className="center">Last attack ({this.state.dynamic.attack.last.timestamp})</h3>
                                    </div>
                                 </div>
                                 <hr />
                                 <div className="row">
                                    <ButtonHomeInfoBox {...this.state.static.sensor}  {...this.state.dynamic.sensor} {...this.state.dynamicButton.sensor}  onClick={this.handleSelectedDetail.bind(null,'sensor')} />
                                    <ButtonHomeInfoBox {...this.state.static.pattern}  {...this.state.dynamic.pattern} onClick={this.handleSelectedDetail.bind(null,'pattern')} {...this.state.dynamicButton.pattern}  />
                                    <ButtonHomeInfoBox {...this.state.static.parameter}  {...this.state.dynamic.parameter} onClick={this.handleSelectedDetail.bind(null,'parameter')} {...this.state.dynamicButton.parameter}  />
                                    <div className="col-md-3">
                                       <ButtonHomeLineHorizontal  onClick={this.handleSelectedDetail.bind(null,'ip')} {...this.state.static.ip} {...this.state.dynamicButton.ip}   />
                                       <ButtonHomeLineHorizontal onClick={this.handleSelectedDetail.bind(null,'method')} {...this.state.static.method}  {...this.state.dynamicButton.method} />
                                       <ButtonHomeLineHorizontal onClick={this.handleSelectedDetail.bind(null,'tools')} {...this.state.static.tools}   {...this.state.dynamicButton.tools} />
                                    </div>
                                 </div>
                                 <div className="row">
                                    <div className="col-md-12">
                                       <ButtonHomeLineHorizontal  onClick={this.handleSelectedDetail.bind(null,'none')} {...this.state.static.home} {...this.state.dynamicButton.home}   />
                                    </div>
                                 </div>
                              </div>
                           </div>
                     </div>

                      <div className="col-md-offset-1 col-md-10  col-sm-12 col-xs-12">
                        <div className="box box-default">
                           <div className="box-header with-border">
                              <i className= {detailActive.icon}  > </i>
                              <h3 className="box-title"> {detailActive.label}</h3>
                           </div>

                           <div className="box-body " id="detail-content">
                              {detailContent}
                           </div>
                        </div>
                     </div>
                     </section>
                  </div>
                  );
            }
         });

   