import './bootstrap';
import Chart from 'chart.js/auto';
import annotationPlugin from 'chartjs-plugin-annotation'; // ✅ 추가
Chart.register(annotationPlugin); // ✅ 등록

document.addEventListener("DOMContentLoaded", async () => {
    // ===== 누적 수익 그래프 =====
    const ctx = document.getElementById('profitChart');
    if (!ctx) return;

    try {
        const response = await fetch('/main/chartData');
        const data = await response.json();

        const cumulative = [];
        data.profits.reduce((acc, val) => {
            const next = acc + val;
            cumulative.push(next);
            return next;
        }, 0);

        data.profits = cumulative;

        const chartCtx = ctx.getContext('2d');

        // 선 아래쪽 그라데이션
        const gradient = chartCtx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(59,130,246,0.4)');
        gradient.addColorStop(1, 'rgba(59,130,246,0)');

        // Chart 생성
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: '누적 수익 그래프',
                    data: data.profits,
                    borderColor: '#3b82f6',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    fill: true,
                    tension: 0.35,
                    pointRadius: (context) =>
                        context.dataIndex === data.profits.length - 1 ? 4 : 3,
                    pointBackgroundColor: (context) =>
                        context.dataIndex === data.profits.length - 1 ? '#3b82f6' : '#60a5fa',
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#1e40af',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        display: false,
                        ticks: {
                            autoSkip: true,
                            maxTicksLimit: 10
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#4b5563',
                            callback: value => value.toLocaleString() + '₩'
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)',
                            drawBorder: false
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleColor: '#f1f5f9',
                        bodyColor: '#e2e8f0',
                        borderColor: '#3b82f6',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false
                    }
                },
                elements: {
                    line: { tension: 0.35 },
                    point: { radius: 3 }
                }
            }
        });

        // ✅ 마지막 점 반짝이게 (색상만 점멸)
        let bright = false;
        setInterval(() => {
            const dataset = chart.data.datasets[0];
            const total = dataset.data.length;
            if (total === 0) return;

            dataset.pointBackgroundColor = dataset.data.map((_, i) => {
                if (i === total - 1) {
                    return bright ? '#60a5fa' : '#1d4ed8';
                }
                return '#60a5fa';
            });

            chart.update('none');
            bright = !bright;
        }, 600);

    } catch (error) {
        console.error('차트 데이터 로드 실패:', error);
    }

    // ===== 승률 도넛 그래프 =====
    const donut = document.getElementById("resultDonutChart");
    if (!donut) return;

    const wins = parseInt(donut.dataset.wins);
    const losses = parseInt(donut.dataset.losses);
    const draws = parseInt(donut.dataset.draws);
    const total = wins + losses + draws;
    const winRate = total > 0 ? Math.round((wins / total) * 100) : 0;

    const chart = new Chart(donut, {
        type: "doughnut",
        data: {
            labels: ["적중", "미적중", "적특"],
            datasets: [{
                data: [wins, losses, draws],
                backgroundColor: [
                    "rgba(59,130,246,0.85)",  // 파랑
                    "rgba(244,63,94,0.85)",   // 로즈레드
                    "rgba(156,163,175,0.5)"   // 회색
                ],
                borderWidth: 0,
                hoverOffset: 10,
                borderRadius: 6,
            }]
        },
        options: {
            cutout: "72%",
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: "#1e293b",
                    titleColor: "#f1f5f9",
                    bodyColor: "#e2e8f0",
                    borderColor: "#3b82f6",
                    borderWidth: 1,
                    callbacks: {
                        label: (ctx) => `${ctx.label}: ${ctx.parsed}회`
                    }
                }
            },
            animation: {
                animateRotate: true,
                duration: 1200,
                easing: "easeOutCubic"
            }
        },
        plugins: [{
            id: "centerText",
            afterDraw(chart, args, opts) {
                const { ctx, chartArea: { width, height } } = chart;
                ctx.save();
                ctx.font = "bold 20px 'Chiron Sung HK', sans-serif";
                ctx.fillStyle = "#334155";
                ctx.textAlign = "center";
                ctx.fillText(`${winRate}%`, width / 2, height / 2 + 8);
                ctx.font = "12px 'Chiron Sung HK'";
                ctx.fillStyle = "#9ca3af";
                ctx.fillText("승률", width / 2, height / 2 + 28);
                ctx.restore();
            }
        }]
    });

    // ===== 폴더수별 적중률 비교 =====
    const folderCtx = document.getElementById('folderChart');
    if (folderCtx) {
        const response = await fetch('/record/chartFolder');
        const data = await response.json();

        new Chart(folderCtx, {
            type: 'bar',
            data: {
                labels: data.labels.map(v => v + '폴더'),
                datasets: [{
                    label: '적중률(%)',
                    data: data.rates,
                    backgroundColor: 'rgba(37,99,235,0.7)',
                    borderRadius: 5,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: v => v + '%' },
                        max: 100,
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: { grid: { display: false } }
                },
                plugins: { legend: { display: false } }
            }
        });
    }

});




