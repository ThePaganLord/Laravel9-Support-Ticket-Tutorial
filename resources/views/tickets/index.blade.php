<x-app-layout>
    <div class="max-w-xl mx-auto p-4 sm:p-12 lg:p-14">
        <table align="center">
            @if(auth()->user()->is_admin)
                <tr>
                    <td colspan="2">
                        <button class="button" onclick="location.href='{{ url('admin_tickets') }}'">All Tickets</button>
                    </td>
                </tr>
                    <!--See all <a href="{{ url('admin/tickets') }}">tickets</a>-->
            @else
                <tr>
                    <td>
                        <button class="button" onclick="location.href='{{ url('my_tickets') }}'">See all your tickets</button>  
                    </td>
                    <td>
                        <button class="button" onclick="location.href='{{ url('new_ticket') }}'">Open a new Ticket</button>
                    </td>
                </tr>
            @endif
        </table>
    </div>
</x-app-layout>
cccc