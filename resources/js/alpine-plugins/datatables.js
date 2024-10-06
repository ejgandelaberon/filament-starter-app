import DataTable from 'datatables.net-dt';

export default (Alpine) => {
    Alpine.data('datatables', ({livewireId, data = [], columns = [], ajax = null, getRecordsUsing = null}) => ({
        init() {
            let ajaxCallback = ajax;

            if (getRecordsUsing !== null) {
                ajaxCallback = async function (data, callback) {
                    callback({
                        data: await Livewire.find(livewireId).call(getRecordsUsing),
                    });
                };
            }

            this.table = new DataTable(this.$refs.table, {
                paging: true,
                info: true,
                searching: true,
                serverSide: true,
                order: [
                    [0, 'desc'],
                ],
                ajax: {
                    url: ajaxCallback,
                    data: {
                        foo: 'bar',
                    },
                    dataSrc: 'data',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                },
                data,
                columns,
            });
        },
    }));
};
