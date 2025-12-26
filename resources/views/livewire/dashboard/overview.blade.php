<div id="dashboard-overview">
  <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

    <!-- Cards -->
    <div class="col-span-1 p-4 bg-white rounded-lg shadow-sm dark:bg-gray-800">
      <div class="text-sm text-gray-500 dark:text-gray-400">Groups Created (You)</div>
      <div class="text-3xl font-bold mt-2">{{ $this->createdGroupsCount }}</div>
      <div class="mt-3 text-sm text-gray-600 dark:text-gray-300">Groups you own and manage.</div>
    </div>

    <div class="col-span-1 p-4 bg-white rounded-lg shadow-sm dark:bg-gray-800">
      <div class="text-sm text-gray-500 dark:text-gray-400">Groups You Joined</div>
      <div class="text-3xl font-bold mt-2">{{ count($this->joinedGroups) }}</div>
      <div class="mt-3 text-sm text-gray-600 dark:text-gray-300">Groups where you are a member but not the owner.</div>
    </div>

    <div class="col-span-1 p-4 bg-white rounded-lg shadow-sm dark:bg-gray-800">
      <div class="text-sm text-gray-500 dark:text-gray-400">Upcoming Payout Dates</div>
      <div class="text-3xl font-bold mt-2">{{ array_sum($this->upcomingChartData ?? []) }}</div>
      <div class="mt-3 text-sm text-gray-600 dark:text-gray-300">Members scheduled for payout in the next 30 days.</div>
    </div>

  </div>


  <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-2">
    <div class="bg-white p-4 rounded-lg shadow-sm dark:bg-gray-800">
      <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Groups by member count</h3>
      <div class="mt-4">
        <div id="groupsChart" wire:ignore style="height:260px"></div>
      </div>

      <div class="mt-4 grid grid-cols-2 gap-4">
        <div class="text-center">
          <h4 class="text-xs text-gray-500 dark:text-gray-400">Paid vs Unpaid</h4>
          <div id="paidChart" wire:ignore style="height:160px"></div>
        </div>
        <div class="text-center">
          <h4 class="text-xs text-gray-500 dark:text-gray-400">Owned vs Joined</h4>
          <div id="ownedJoinedChart" wire:ignore style="height:160px"></div>
        </div>
      </div>

      <div class="mt-4 text-xs text-gray-500 dark:text-gray-400">Showing groups you own.</div>
    </div>

    <div class="bg-white p-4 rounded-lg shadow-sm dark:bg-gray-800">
      <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Upcoming payouts (date vs members)</h3>
      <div class="mt-4">
        <div id="upcomingChart" wire:ignore style="max-height:300px"></div>
      </div>

      <div class="mt-4">
        <ul class="text-sm space-y-2">
          @foreach($this->upcomingList as $group)
            <li class="flex items-start justify-between">
              <div class="font-semibold">{{ $group['label'] }}</div>
              <div class="text-gray-500">{{ $group['count'] }} member(s)</div>
              <div class="w-full mt-2">
                <ul class="text-xs text-gray-600 dark:text-gray-400">
                  @foreach($group['items'] as $item)
                    <li>• {{ $item['member_name'] }} — <span class="font-medium">{{ $item['group_name'] }}</span></li>
                  @endforeach
                </ul>
              </div>
            </li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script>
    document.addEventListener('livewire:load', function () {
      const palette = ['#3b82f6', '#6366f1', '#10b981', '#f97316', '#f59e0b', '#ef4444', '#06b6d4'];

      const groupsLabels = @json($this->groupsChartLabels);
      const groupsData = @json($this->groupsChartData);
      const upLabels = @json($this->upcomingChartLabels);
      const upData = @json($this->upcomingChartData);
      const upcomingTooltipMap = @json($this->upcomingTooltipMap);
      const paid = @json($this->paidCount);
      const unpaid = @json($this->unpaidCount);
      const owned = @json($this->createdGroupsCount);
      const joined = @json($this->joinedGroupsCount);

      const isDark = document.documentElement.classList.contains('dark') || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
      const textColor = isDark ? '#E5E7EB' : '#111827';
      const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(17,24,39,0.06)';

      // Groups by member count (bar)
      const groupsOptions = {
        chart: { type: 'bar', height: 260, toolbar: { show: false }, foreColor: textColor, animations: { enabled: true } },
        series: [{ name: 'Members', data: groupsData }],
        xaxis: { categories: groupsLabels, labels: { style: { colors: groupsLabels.map(() => textColor) }, formatter: function(val){ return val && val.length > 20 ? val.slice(0,20) + '…' : val; } } },
        plotOptions: { bar: { borderRadius: 6 } },
        colors: groupsLabels.map((_,i) => palette[i % palette.length]),
        dataLabels: { enabled: false },
        grid: { strokeDashArray: 4, borderColor: gridColor },
        tooltip: { theme: isDark ? 'dark' : 'light' }
      };

      const groupsChart = new ApexCharts(document.querySelector('#groupsChart'), groupsOptions);
      groupsChart.render();

      // Upcoming payouts (line) with tooltip list
      const upcomingOptions = {
        chart: { type: 'line', height: 220, toolbar: { show: false }, foreColor: textColor },
        series: [{ name: 'Members', data: upData }],
        xaxis: { categories: upLabels, labels: { style: { colors: upLabels.map(() => textColor) }, formatter: function(val){ return val && val.length > 12 ? val.slice(0,12) + '…' : val; } } },
        stroke: { curve: 'smooth' },
        markers: { size: 4 },
        grid: { borderColor: gridColor },
        tooltip: {
          theme: isDark ? 'dark' : 'light',
          x: { show: true },
          y: { formatter: function(val){ return val + ' members'; } },
          custom: function({ series, seriesIndex, dataPointIndex, w }){
            const label = upLabels[dataPointIndex];
            const lines = upcomingTooltipMap[label] || [];
            let html = '\u003Cdiv style="padding:6px;">';
            html += '\u003Cdiv style="font-weight:600;margin-bottom:6px;">' + (label || '') + '\u003C/div>';
            html += '\u003Cdiv style="font-size:12px;color:var(--tw-text-opacity, #555);">' + (series[seriesIndex][dataPointIndex] || 0) + ' member(s)\u003C/div>';
            if (lines.length) {
              html += '\u003Cdiv style="margin-top:8px; font-size:12px;">';
              lines.forEach(function(l){ html += '\u003Cdiv>• ' + l + '\u003C/div>'; });
              html += '\u003C/div>';
            }
            html += '\u003C/div>';
            return html;
          }
        }
      };

      const upcomingChart = new ApexCharts(document.querySelector('#upcomingChart'), upcomingOptions);
      upcomingChart.render();

      // Paid vs Unpaid donut
      const paidOptions = {
        chart: { type: 'donut', height: 160, toolbar: { show: false }, foreColor: textColor },
        series: [paid, unpaid],
        labels: ['Paid','Not Paid'],
        colors: ['#10b981', '#ef4444'],
        legend: { position: 'bottom', labels: { colors: textColor } },
        plotOptions: { pie: { donut: { size: '65%' } } },
        tooltip: { theme: isDark ? 'dark' : 'light' }
      };

      const paidChart = new ApexCharts(document.querySelector('#paidChart'), paidOptions);
      paidChart.render();

      // Owned vs Joined donut
      const ojOptions = {
        chart: { type: 'donut', height: 160, toolbar: { show: false }, foreColor: textColor },
        series: [owned, joined],
        labels: ['Owned','Joined'],
        colors: ['#3b82f6', '#6366f1'],
        legend: { position: 'bottom', labels: { colors: textColor } },
        plotOptions: { pie: { donut: { size: '65%' } } },
        tooltip: { theme: isDark ? 'dark' : 'light' }
      };

      const ownedJoinedChart = new ApexCharts(document.querySelector('#ownedJoinedChart'), ojOptions);
      ownedJoinedChart.render();

      // Observe theme changes and update charts
      const applyTheme = () => {
        const dark = document.documentElement.classList.contains('dark') || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
        const txt = dark ? '#E5E7EB' : '#111827';
        const grid = dark ? 'rgba(255,255,255,0.06)' : 'rgba(17,24,39,0.06)';
        const themeMode = dark ? 'dark' : 'light';

        groupsChart.updateOptions({ chart: { foreColor: txt }, xaxis: { labels: { style: { colors: groupsLabels.map(() => txt) } } }, grid: { borderColor: grid }, tooltip: { theme: themeMode } });
        upcomingChart.updateOptions({ chart: { foreColor: txt }, xaxis: { labels: { style: { colors: upLabels.map(() => txt) } } }, grid: { borderColor: grid }, tooltip: { theme: themeMode } });
        paidChart.updateOptions({ chart: { foreColor: txt }, legend: { labels: { colors: txt } }, tooltip: { theme: themeMode } });
        ownedJoinedChart.updateOptions({ chart: { foreColor: txt }, legend: { labels: { colors: txt } }, tooltip: { theme: themeMode } });
      };

      const obs = new MutationObserver(applyTheme);
      obs.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

      applyTheme();
    });
  </script>
</div>