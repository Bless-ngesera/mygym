@component('mail::message')
# Welcome to MyGym, Dear {{ $user->name }} 🎉

We're excited to have you on board!

You can now:
- Book classes
- Manage your schedule
- Track your fitness journey
- Connect with instructors

@component('mail::button', ['url' => url('/')])
Visit MyGym
@endcomponent

Thanks,  
The MyGym Team
@endcomponent
