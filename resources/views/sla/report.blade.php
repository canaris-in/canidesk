@extends('layouts.app')
@section('content')
<div class="container report-container">
    <p style="font-weight: bold;width: 20%;float: left;">SLA REPORT</p>
    <table class="table datatable table-borderless slatable" >
        <thead>
            <tr>
                <th class="custom-cell">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="selectAll">
                    </div>
                </th>
                <th class="custom-cell">TICKET NO</th>
                <th class="custom-cell">STATUS</th>
                <th class="custom-cell">Priority</th>
                <th class="custom-cell">ENGINEER</th>
                <th class="custom-cell">CATEGORY</th>
                <th class="custom-cell">SUBJECT</th>
                <th class="custom-cell">Mailbox</th>
                <th class="custom-cell">Escalated</th>
                <th class="custom-cell">Created date</th>
                <th class="custom-cell">RESOLUTION TIME</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tickets as $ticket)
            @php
                $dataArray = json_decode($ticket->conversationCustomField, true);
                $ticketPriorityArray =json_decode($ticket->conversationPriority, true);
                $ticketCategoryArray =json_decode($ticket->conversationCategory, true);
                $ticketEscalated =json_decode($ticket->conversationEscalated, true);
                $status = $ticket['status'] == 1 ? 'ACTIVE' : ($ticket['status'] == 2 ? 'PENDING' : ($ticket['status'] == 3 ? 'CLOSED' : 'SPAM'));
                $createdAt = \Carbon\Carbon::parse($ticket['created_at']);
                $lastReplyAt = \Carbon\Carbon::parse($ticket['last_reply_at']);
                $duration = $lastReplyAt->diff($createdAt);
            @endphp
            @foreach ($dataArray as $item)
                @php
                    $customField = $item['custom_field'];
                    $options = $customField['options'];
                    $name = $customField['name'];
                    $value = $item['value'];
                    $optionValue = null;
                    foreach ($options as $key => $option) {
                        if ($key == $value) {
                            $optionValue = $option;
                            break;
                        }
                    }
                @endphp
            @endforeach



            @foreach ($ticketCategoryArray as $item)
            @php

                $options = $item['options'];
                $ticketCategory = null;
                foreach ($options as $key => $option) {
                    if ($key == $value) {
                        $ticketCategory = $option;
                        break;
                    }
                }
            @endphp

        @endforeach

            @foreach ($ticketPriorityArray as $item)
            @php

                $options = $item['options'];
                $ticketPriority = null;
                foreach ($options as $key => $option) {
                    if ($key == $value) {
                        $ticketPriority = $option;
                        break;
                    }
                }
            @endphp

        @endforeach

        @foreach ($ticketEscalated as $item)
        @php

            $options = $item['options'];
            $ticketEscalate = null;
            foreach ($options as $key => $option) {
                if ($key == $value) {
                    $ticketEscalate = $option;
                    break;
                }
            }
        @endphp

    @endforeach
        @php
            $rtime=$duration->format('%h HRS');
            $restime=null;
            if($rtime==0){
                $restime="N/A";
            }else{
                $restime=$rtime;
            }
        @endphp
            <tr>
                <td class="custom-cell">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="">
                        <label class="form-check-label" for="defaultCheck1">
                        </label>
                    </div>
                </td>
                <td class="custom-cell">#{{$ticket->number}}</td>
                <td class="custom-cell"><span class="tag tag-{{ $status }}">{{$status}}</span></td>
                <td class="custom-cell">{{isset($ticketPriority) ? $ticketPriority : '-'}}</td>
                <td class="custom-cell">{{$ticket->user ? $ticket->user->first_name . ' ' . $ticket->user->last_name : "-"}}</td>
                <td class="custom-cell">{{isset($ticketCategory) ? $ticketCategory : '-'}}</td>
                <td class="custom-cell">{{$ticket->subject}}</td>
                <td class="custom-cell">{{$ticket->user ? $ticket->user->email : "-"}}</td>
                <td class="custom-cell">{{isset($ticketEscalate) ? 'YES' : 'NO'}}</td>
                <td class="custom-cell">{{$ticket->created_at}}</td>
                <td class="custom-cell">{{$restime}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
<style>
   .dm .slatable{
    background-color: #1d1c24;
   }
   .slatable{
    background-color: #eeeeee;
   }
   .custom-cell{
    font-size: 12px;
   }
   .dm button.dt-button, div.dt-button, a.dt-button, input.dt-button{
    color: snow;
   }
   .dm .pagination > li > a, .pagination > li > span{
      background: #1d1c24;
      color: #8bb4dd;
   }
   .dm .report-container{
    background: #1d1c24;
   }
   .report-container{
    background-color: #eeeeee;
    padding: 1.3em;
    width: 90%;
   }
   .dm .input-sm{
    border-radius: 3px;
   }
</style>
@endsection

@section('javascript')



<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/vfs_fonts.js"></script>
<script>
$(document).ready(function() {
    $('.datatable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'csv',
            {
                extend: 'pdfHtml5',
                text: 'PDF',
                orientation:'landscape',
                exportOptions: {
                    columns: ':visible'
                },
            }
        ]
    });
});
</script>

@endsection
