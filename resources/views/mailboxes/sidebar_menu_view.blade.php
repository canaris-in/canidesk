<div class="alert alert-danger  alert-floating errormsg" id="alert">
    <div class="glyphicon-msg">
        <i class="glyphicon glyphicon-exclamation-sign"></i>
        <p id="errorMsg"></p>
    </div>
</div>
<div class="dropdown sidebar-title sidebar-title-extra">
    <span class="sidebar-title-extra-value active-count">{{ $folder->getTypeName() }}
        ({{ $folder->active_count }})</span>
    @action('mailbox.view.before_name', $mailbox)
    <span class="sidebar-title-real mailbox-name">@include('mailboxes/partials/mute_icon', ['mailbox' => $mailbox]){{ $mailbox->name }}</span>
    <span class="sidebar-title-email">{{ $mailbox->email }}</span>
</div>
<ul class="sidebar-menu" id="folders">
    @include('mailboxes/partials/folders')
</ul>
@php
    $show_settings_btn = Auth::user()->can('viewMailboxMenu', Auth::user());
@endphp
@if (\Eventy::filter('mailbox.show_buttons', true, $mailbox))
    <div class="sidebar-buttons btn-group btn-group-justified @if ($show_settings_btn) has-settings @endif">
        @if ($show_settings_btn)
            <div class="btn-group dropdown" data-toggle="tooltip" title="{{ __('Mailbox Settings') }}">
                <a class="btn btn-trans dropdown-toggle" data-toggle="dropdown" href="#"><i
                        class="glyphicon glyphicon-cog"></i> <b class="caret"></b></a>
                <ul class="dropdown-menu" role="menu">
                    @include('mailboxes/settings_menu', ['is_dropdown' => true])
                </ul>
            </div>
        @endif
        <a class="btn btn-trans" href="{{ route('conversations.create', ['mailbox_id' => $mailbox->id]) }}"
            aria-label="{{ __('New Conversations') }}" data-toggle="tooltip" title="{{ __('New Tickets') }}"
            role="button"><i class="glyphicon glyphicon-envelope"></i></a>
        <a class="btn btn-trans" href="javascript:void(0)"
            data-url="{{ route('mailboxes.fetchMail', ['mailbox_id' => $mailbox->id]) }}" data-toggle="tooltip"
            title="{{ __('Fetch Tickets') }}" role="button" name="action" value="fetchEmail"><i
                class="glyphicon glyphicon-refresh " id="fetchEmail" onclick="myFunction()"></i></a>
    </div>
@endif
@action('mailbox.after_sidebar_buttons')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
    .errormsg {
        display: none !important;
        border-radius: 3px;
    }

    .glyphicon-msg {
        display: flex;
    }

    .glyphicon-exclamation-sign {
        margin-top: 1px;
    }
</style>
<script>
    function myFunction() {
        document.getElementById("alert").style.display = "inline-block !important";
    }
    $(document).ready(function() {
        document.getElementById("alert").style.display = "flex";
        $('a[name="action"]').on('click', function(e) {
            e.preventDefault();
            var $button = $(this);
            var url = $button.data('url');
            $button.find('.glyphicon').addClass('glyphicon-spin');

            $.ajax({
                url: url,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    // Update the UI or perform any necessary actions with the fetched tickets
                    const data = response;

                    // Extract the log string from the data object
                    const logString = data[1];

                    // Define the regular expression pattern
                    const errorPattern = /Error: (.*?);/;

                    // Extract the error message using match()
                    const match = logString.match(errorPattern);

                    // Retrieve the error message
                    const errorMessage = match && match[1] ? match[1] :
                        "No error message found";

                    //popupbox
                    var popup = document.getElementById("alert");
                    popup.style.display = "flex";

                    // Function to handle clicks outside the popup box
                    function handleClickOutside(event) {
                        if (!popup.contains(event.target)) {
                            popup.style.display = "none";
                        }
                    }

                    // Event listener to close the popup when clicked outside
                    document.addEventListener("click", handleClickOutside);
                    //msg send to html file
                    document.getElementById("errorMsg").innerText = errorMessage;
                    $button.find('.glyphicon').removeClass('glyphicon-spin');
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    // Display an error message or handle the error in an appropriate way
                    document.getElementById("errorMsg").innerText = error;
                    $button.find('.glyphicon').removeClass('glyphicon-spin');
                }
            });
        });
    });
</script>
