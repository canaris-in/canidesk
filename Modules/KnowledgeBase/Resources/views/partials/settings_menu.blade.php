@if (Auth::user()->isAdmin()||Auth::user()->isITHead())
<li @if (\App\Misc\Helper::isMenuSelected('knowledgebase'))class="active"@endif><a href="{{ route('mailboxes.knowledgebase.settings', ['mailbox_id'=>$mailbox->id]) }}"><i class="glyphicon glyphicon-book"></i> {{ __('Knowledge Base') }}</a></li>
@endif
