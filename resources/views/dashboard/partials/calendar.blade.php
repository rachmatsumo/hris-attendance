<div class="col-12 col-lg-6 col-md-6 mb-3 d-flex">
    <div class="table-container w-100">  

        <div class="p-3 border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                @php
                    $firstDate = \Carbon\Carbon::parse($schedules->first()?->work_date);
                @endphp
                <h5 class="mb-0"><i class="fas fa-history me-2"></i> Work Schedule - {{ \Carbon\Carbon::parse($schedules->first()?->work_date)?->locale('id')->translatedFormat('F Y') }}</h5> 
            </div>
        </div>

        <div class="table-responsive">

            <table class="table text-center" style="font-size:12px;">
                <thead>
                    <tr>
                        <th>Senin</th>
                        <th>Selasa</th>
                        <th>Rabu</th>
                        <th>Kamis</th>
                        <th>Jumat</th>
                        <th>Sabtu</th>
                        <th>Minggu</th>
                    </tr>
                </thead>
                <tbody id="calendar-body">
                    @php
                        $firstDate = \Carbon\Carbon::parse($dates?->first());
                        $lastDate = \Carbon\Carbon::parse($dates?->last());
                        $currentDate = $firstDate->copy();
                        $week = [];
                    @endphp

                    @while($currentDate <= $lastDate)
                        @php
                            $dayOfWeek = $currentDate->dayOfWeek; // 0=Sun ... 6=Sat
                            $dayIndex = ($dayOfWeek + 6) % 7; // Konversi: Senin=0 ... Minggu=6
                        @endphp

                        {{-- Awal minggu pertama, tambahkan padding kosong --}}
                        @if($currentDate->eq($firstDate) && $dayIndex > 0)
                            @for($i=0; $i < $dayIndex; $i++)
                                @php $week[] = '<td class="bg-light"></td>'; @endphp
                            @endfor
                        @endif

                        {{-- Cell tanggal --}}
                        @php
                            $shift = $schedules->where('work_date', $currentDate->format('Y-m-d'))->first();
                            $color = $shift?->workingTime?->color ?? '#e9ecef';
                            $textColor = in_array($color, ['#ffc107','#e9ecef']) ? 'black' : 'white';
                            $shiftText = $shift
                                ? ($shift->workingTime?->code.' <br> '.$shift->workingTime?->name)
                                : 'Libur';
                            $week[] = '<td style="background-color:'.$color.'; color:'.$textColor.'; min-width:50px;">'.
                                        $currentDate->format('d').'<br><small>'.$shiftText.'</small></td>';
                        @endphp

                        {{-- Jika hari Minggu, push baris --}}
                        @if($dayIndex == 6)
                            <tr>{!! implode('', $week) !!}</tr>
                            @php $week = []; @endphp
                        @endif

                        @php $currentDate->addDay(); @endphp
                    @endwhile

                    {{-- Baris terakhir jika minggu tidak penuh --}}
                    @if(!empty($week))
                        @php
                            while(count($week) < 7) $week[] = '<td class="bg-light"></td>';
                            echo '<tr>'.implode('', $week).'</tr>';
                        @endphp
                    @endif

                </tbody>
            </table>

        </div>
    </div>
</div>