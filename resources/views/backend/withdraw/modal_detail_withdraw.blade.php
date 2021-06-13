<div aria-labelledby="mySmallModalLabel" data-keyboard="false" data-backdrop="static" class="modal fade detail-modal-{{$wd->id}}" role="dialog" tabindex="-1" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      	<div class="modal-header">
	        <h5 class="modal-title text-white"><i class="fa fa-info-circle"></i> Information</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	    </div>
	    <div class="modal-body">
	      	<div class="row">
	      		<div class="col-md-6">
      				<h6>Detail Withdrawal</h6>
	      			<div class="panel">
		              	<div class="panel-body">
				      		<p class="text-dark">Name : {{ucfirst($wd->user->name)}}</p>
				      		<p class="text-dark">Username : {{ucfirst($wd->user->username)}}</p>
				      		<p class="text-dark">Amount ($) : {{number_format($wd->amount,2)}}</p>
				      		<p class="text-dark">Exchange Rate : {{number_format($wd->price,2)}}</p>
				      		<p class="text-dark">Total : {{number_format($wd->total,2)}}</p>
				      		<p class="text-dark">Fee : {{number_format($wd->fee,2)}}</p>
				      		<p class="text-dark">Receive : {{number_format($wd->receive,2)}}</p>
				      		<p class="text-dark">Status :
				      			@if($wd->status == 1)
			                    	<span class="badge p-1 badge-success">Success</span>
				                @elseif($wd->status == 2)
				                    <span class="badge p-1 badge-danger">Canceled</span>
				                @else
				                    <span class="badge p-1 badge-warning">Pending</span>
				                @endif
			                </p>
				      		<p class="text-dark">Description : {{$wd->description}}</p>
				      		<p class="text-dark">Date : {{$wd->created_at}}</p>
		              	</div>
		            </div>
	      		</div>
	      		<div class="col-md-6">
      				<h6>Account Bank</h6>
	      			<div class="panel">
		              	<div class="panel-body">
		              		@php
		              			$json = json_decode($wd->json_data);
		              		@endphp
                            @if ($wd->type == 'bank')
                                <p class="text-dark">Bank Name : {{$json->bank_name}}</p>
				      		    <p class="text-dark">Bank Username : {{$json->account_name}}</p>
				      		    <p class="text-dark">Bank Account : {{$json->account_number}} <span class="badge p-1 badge-success"  onclick="copyToClipboard('{{$json->account_number}}')">Copy <i class="la la-copy icon-sm text-primary"></i></span></p>
                            @else
                                <p class="text-dark">USDT Address : {{$json->usdt_address}} <span class="badge p-1 badge-success"  onclick="copyToClipboard('{{$json->usdt_address}}')">Copy <i class="la la-copy icon-sm text-primary"></i></span></p>
                            @endif

                            @isset($json->txid)
                            <p class="text-dark">Txid/Ref : {{$json->txid}} <span class="badge p-1 badge-success" onclick="copyToClipboard('{{$json->txid}}')">Copy <i class="la la-copy icon-sm text-primary"></i></span></p>
                            @endisset
		              	</div>
		            </div>
	      		</div>
	      		<hr>
			</div>
	    </div>
	    <div class="modal-footer" id="footer-md">
	    	@if($wd->status == 0)
        	<button class="btn btn-success" type="button" onclick="accept('{{$wd->id}}','{{ucfirst($wd->user->username)}}');"> Accept</button>
			<button class="btn btn-warning" type="button" onclick="reject('{{$wd->id}}','{{ucfirst($wd->user->username)}}');"> Reject</button>
			@endif
			<button class="btn btn-danger" data-dismiss="modal" type="button"> Close</button>
	  	</div>
    </div>
  </div>
</div>
