<?php

namespace App\View\Components\Backend;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ContactCard extends Component
{
    public $class, $contactName, $email, $phone, $cardStyle, $contactId;

    /**
     * Create a new component instance.
     */
    public function __construct($contactName, $email, $phone, $contactId, $class = null, $cardStyle = 'horizontal')
    {
        $this->class = $class;
        $this->contactName = $contactName;
        $this->email = $email;
        $this->phone = $phone;
        $this->cardStyle = $cardStyle;  // Vertical | Horizontal
        $this->contactId = $contactId;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.backend.contact-card');
    }
}
