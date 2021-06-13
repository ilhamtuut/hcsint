<div class="modal fade" id="modal-question" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title text-white"><i class="icon_question_alt2"></i> Secret Question</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body">
              <div class="form-group">
                  <label class="text-muted">Question</label>
                  <select name="question" style="width: 100%;" class="selectpicker" data-style="btn-select-tag">
                    <option value="">Select Question</option>
                    @foreach ($question as $item)
                        <option value="{{$item->id}}">{{$item->name}}</option>
                    @endforeach
                </select>
                  {{-- <input type="text" value="{{Auth::user()->question->question->name}}" class="form-control" placeholder="Question" readonly> --}}
              </div>
              <div class="form-group">
                  <label class="text-muted">Answer</label>
                  <input type="text" name="answer" class="form-control" placeholder="Answer">
              </div>
              <div class="form-group">
                  <label class="text-muted">PIN Authenticator</label>
                  <input type="password" name="pin_authenticator" class="form-control" placeholder="PIN Authenticator">
              </div>
          </div>
          <div class="modal-footer">
            <div id="action">
                <button type="submit" class="btn btn-warning rounded-0" id="btn_submit">Submit</button>
                <button type="button" class="btn btn-danger rounded-0" data-dismiss="modal">Cancel</button>
            </div>
            <i class="hidden" id="spinner"><span class="fa fa-spin fa-spinner"></span></i>
          </div>
        </div>
    </div>
  </div>
