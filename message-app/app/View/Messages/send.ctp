<div>
    <br /> <br />
    <button class="btn btn-info mb-4" ng-click="backToMessageList()">Message List</button>
    <h5>Send New Message</h5>
    <br />
    <label for="exampleFormControlTextarea1">Recipient</label>
    <select ng-model="selectedRecipient" class="js-example-responsive" multiple="multiple" style="width: 98%" select2></select>
    <div class="form-group mt-4">
        <label for="exampleFormControlTextarea1">Message</label>
        <textarea ng-model="textMessage" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
    </div>
    <div class="d-flex justify-content-end">
        <button class="btn btn-success px-4" style="z-index: 1;" ng-click="sendMessage()">Send Message</button>
    </div>
</div>
