<x-mail::message>
# New contact message

**From:** {{ $name }}  
**Email:** {{ $email }}  
@if($phone)
**Phone:** {{ $phone }}  
@endif

---

{{ $message }}

---

Reply directly to this email to respond to the customer.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
