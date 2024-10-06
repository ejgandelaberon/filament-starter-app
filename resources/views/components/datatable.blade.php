<div
    x-data="datatables({
                livewireId: @js($livewireId),
                data: @js($data),
                columns: @js($columns),
                ajax: @js($ajax),
                getRecordsUsing: @js($getRecordsUsing)
            })"
    wire:ignore
    class="table w-full h-full border-2 rounded-lg"
>
    <table x-ref="table"></table>
</div>
