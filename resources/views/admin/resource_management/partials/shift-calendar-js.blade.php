<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('shift-patterns');
    const monthPicker = document.getElementById('month-picker');
    const startDaySelect = document.getElementById('start_day');
    const calendarBody = document.getElementById('calendar-body');

    console.log(container, monthPicker, startDaySelect);

    // Data dari Blade
    const workingTimes = @json($workingTimes);
    const shiftPattern = @json($shiftPattern);
    const month = '{{ $month }}';
    startDaySelect.value = '{{ $startDay }}';

    // Fungsi render kalender
    function renderCalendar() {
        const [year, monthNum] = monthPicker.value.split('-').map(Number);
        const firstDate = new Date(year, monthNum-1, 1);
        const lastDate = new Date(year, monthNum, 0);
        const totalDays = lastDate.getDate();

        const shiftPatternArray = Array.from(container.querySelectorAll('select')).map(s => s.value ? parseInt(s.value) : null);
        if (shiftPatternArray.length === 0) {
            calendarBody.innerHTML = '<tr><td colspan="7" class="text-center">Tambahkan pola kerja terlebih dahulu</td></tr>';
            return;
        }

        calendarBody.innerHTML = '';
        let week = [];

        // Minggu mulai Senin
        const weekStart = 1;

        // Padding minggu pertama
        const firstDayWeekday = firstDate.getDay(); // 0=Sun ... 6=Sat
        const offset = (firstDayWeekday - weekStart + 7) % 7;
        for(let i = 0; i < offset; i++) week.push('<td class="text-muted"></td>');

        // Cari tanggal pertama sesuai start_day
        let firstPatternDate = new Date(year, monthNum-1, 1);
        while (firstPatternDate.getDay() !== parseInt(startDaySelect.value)) {
            firstPatternDate.setDate(firstPatternDate.getDate() + 1);
        }

        for(let day = 1; day <= totalDays; day++) {
            const date = new Date(year, monthNum-1, day);
            const daysSincePatternStart = Math.floor((date - firstPatternDate)/(1000*60*60*24));
            let shiftIndex = ((daysSincePatternStart % shiftPatternArray.length) + shiftPatternArray.length) % shiftPatternArray.length;
            let shiftId = shiftPatternArray[shiftIndex];

            let style='', text='';
            if(shiftId===null){
                style='background-color:#e9ecef;';
                text='Libur';
            } else {
                const option = Array.from(container.querySelectorAll('select')[0].options).find(o => parseInt(o.value)===shiftId);
                const color = option?.getAttribute('color') || '#0d6efd';
                style=`background-color:${color}; color:white;`;
                text = workingTimes.find(wt=>wt.id===shiftId)?.name || 'Libur';
            }

            week.push(`<td style="${style}">${day}<br><small>${text}</small></td>`);

            if(week.length % 7 === 0){
                calendarBody.innerHTML += `<tr>${week.join('')}</tr>`;
                week = [];
            }
        }

        if(week.length){
            while(week.length<7) week.push('<td class="text-muted"></td>');
            calendarBody.innerHTML += `<tr>${week.join('')}</tr>`;
        }
    }

    // Event listener
    container.addEventListener('change', renderCalendar);
    monthPicker.addEventListener('change', renderCalendar);
    startDaySelect.addEventListener('change', renderCalendar);

    // Tombol tambah/hapus shift
    document.getElementById('add-shift').addEventListener('click', ()=>{
        const div = document.createElement('div');
        div.classList.add('input-group', 'mb-2');
        div.innerHTML = `
            <select name="shift_pattern[]" class="form-control">
                <option value="">Libur</option>
                @foreach($workingTimes as $wt)
                    <option value="{{ $wt->id }}" color="{{ $wt->color }}">{{ $wt->name }} ({{ $wt->start_time }}-{{ $wt->end_time }})</option>
                @endforeach
            </select>
            <button type="button" class="btn btn-danger remove-shift">Hapus</button>
        `;
        container.appendChild(div);
        div.querySelector('.remove-shift').addEventListener('click', ()=>{ div.remove(); renderCalendar(); });
        renderCalendar();
    });

    document.querySelectorAll('.remove-shift').forEach(btn=>{
        btn.addEventListener('click', function(){ btn.parentElement.remove(); renderCalendar(); });
    });

    renderCalendar();
});
</script>
