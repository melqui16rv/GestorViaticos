<?php
session_start();
ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';

requireRole(['4', '5', '6']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel - Metas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="<?php echo BASE_URL; ?>assets/img/public/logosena.png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/share/dashboard.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/share/dashboard_content.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script>
        // Función para generar la gráfica de barras, ahora recibe el ID del canvas y los datos
        function generarGraficaBarras(canvasId, proyectos, titulo) {
            const ctx = document.getElementById(canvasId).getContext('2d');
            const labels = proyectos.map(p => p.nombre_linea);
            const terminados = proyectos.map(p => Number(p.terminados));
            const enProceso = proyectos.map(p => Number(p.en_proceso));

            const verdeSuave = 'rgba(34,197,94,0.75)';
            const verdeBorde = 'rgba(34,197,94,1)';
            const amarilloSuave = 'rgba(253,224,71,0.65)';
            const amarilloBorde = 'rgba(253,224,71,1)';
            const azulSuave = 'rgba(59,130,246,0.60)';
            const azulBorde = 'rgba(59,130,246,1)';


            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Terminados',
                        data: terminados,
                        backgroundColor: verdeSuave,
                        borderColor: verdeBorde,
                        borderWidth: 1
                    }, {
                        label: 'En Proceso',
                        data: enProceso,
                        backgroundColor: amarilloSuave,
                        borderColor: amarilloBorde,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: titulo, // Usa el título pasado como argumento
                            font: {
                                size: 16
                            }
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'end',
                            font: {
                                size: 12
                            },
                            color: '#222',
                            formatter: (value) => {
                                return value > 0 ? value : '';
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return value;
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        function generarGraficaTorta(containerId, proyectos, titulo) {
            const container = document.getElementById(containerId);
            container.innerHTML = ''; // Limpia el contenedor

            proyectos.forEach(proyecto => {
                const tortaCard = document.createElement('div');
                tortaCard.className = 'torta-card';

                const canvas = document.createElement('canvas');
                const canvasId = `torta-${proyecto.id_linea}`; // Asegura IDs únicos
                canvas.id = canvasId;
                tortaCard.appendChild(canvas);

                const tortaTitle = document.createElement('div');
                tortaTitle.className = 'torta-title';
                tortaTitle.textContent = proyecto.nombre_linea;
                tortaCard.appendChild(tortaTitle);

                const totalLinea = Number(proyecto.terminados) + Number(proyecto.en_proceso);
                const tortaInfo = document.createElement('div');
                tortaInfo.className = 'torta-info';
                tortaInfo.textContent = `Total: ${totalLinea}`;
                tortaCard.appendChild(tortaInfo);

                container.appendChild(tortaCard);

                const ctx = document.getElementById(canvasId).getContext('2d');
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['Terminados', 'En Proceso'],
                        datasets: [{
                            label: 'Proyectos',
                            data: [Number(proyecto.terminados), Number(proyecto.en_proceso)],
                            backgroundColor: [
                                'rgba(34,197,94,0.85)',
                                'rgba(253,224,71,0.75)',
                            ],
                            borderColor: [
                                'rgba(34,197,94,1)',
                                'rgba(253,224,71,1)',
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: false,
                                text: `Gráfica de Torta para ${proyecto.nombre_linea}`,
                                font: {
                                    size: 14
                                }
                            },
                            legend: {
                                position: 'top'
                            },
                            datalabels: {
                                formatter: (value, ctx) => {
                                    const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b);
                                    const percentage = value / total;
                                    return percentage > 0.1 ? `${(percentage * 100).toFixed(1)}%` : '';
                                },
                                color: '#111',
                                font: {
                                    size: 12
                                }
                            }
                        }
                    }
                });
            });
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen relative">
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/nav.php'; ?>

    <button id="sidebarToggle" class="sidebar-toggle-btn" aria-label="Mostrar/Ocultar menú" type="button">
        <svg xmlns="http://www.w3.org/2000/svg" class="toggle-icon text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </button>
    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <div class="flex min-h-screen">
        <aside id="sidebarFilament" class="sidebar-filament bg-white border-r border-gray-200 flex flex-col h-screen fixed lg:static left-0 top-0">
            <div class="flex items-center border-b border-gray-200 relative h-16">
                <span class="text-xl font-bold text-blue-700 mx-auto" style="color: #2b3b4f;">Panel de Metas</span>
            </div>
            <nav class="flex-1 py-4">
                <ul>
                    <li>
                        <a href="#" id="navProyectosTecnologicos" class="sidebar-link flex items-center px-6 py-3 text-gray-700 font-medium cursor-pointer">
                            <i class="ti ti-device-laptop mr-3"></i> 100 Proyectos de Base Tecnológica
                        </a>
                    </li>
                    <li>
                        <a href="#" id="navAsesorarAsociaciones" class="sidebar-link flex items-center px-6 py-3 text-gray-700 font-medium cursor-pointer">
                            <i class="ti ti-users-group mr-3"></i> Asesorar a 20 Asociaciones
                        </a>
                    </li>
                    <li>
                        <a href="#" id="navAsesorarAprendices" class="sidebar-link flex items-center px-6 py-3 text-gray-700 font-medium cursor-pointer">
                            <i class="ti ti-school mr-3"></i> Asesorar a 1 Cooperativa de Aprendices
                        </a>
                    </li>
                    <li>
                        <a href="#" id="navExtensionismo" class="sidebar-link flex items-center px-6 py-3 text-gray-700 font-medium cursor-pointer">
                            <i class="ti ti-rocket mr-3"></i> 5 Proyectos de Extensionismo Tecnológico
                        </a>
                    </li>
                    <li>
                        <a href="#" id="navVisitasAprendices" class="sidebar-link flex items-center px-6 py-3 text-gray-700 font-medium cursor-pointer">
                            <i class="ti ti-route mr-3"></i> Visitas de Aprendices
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="p-4 border-t border-gray-200 text-xs text-gray-500">
                &copy; <?php echo date('Y'); ?> Gestor Tecnoparque
            </div>
        </aside>
        <main id="mainContentFilament" class="flex-1 p-6">
            <div id="contentProyectosTecnologicos" style="display: none;">
                <?php include 'ProyectosTec.php'; ?>
            </div>
            <div id="contentAsesorarAsociaciones" style="display: none;">
                <?php include 'Asociaciones.php'; ?>
            </div>
            <div id="contentAsesorarAprendices" style="display: none;">
                <?php include 'Aprendices.php'; ?>
            </div>
            <div id="contentExtensionismo" style="display: none;">
                <?php include 'ProyectosExt.php'; ?>
            </div>
            <div id="contentVisitasAprendices" style="display: none;">
                <?php include 'Visitas.php'; ?>
            </div>
        </main>
    </div>

    <script>
        $(document).ready(function() {
            $('.sidebar-link').on('click', function() {
                $('.sidebar-link').removeClass('active');
                $(this).addClass('active');

                var targetId = $(this).attr('id').replace('nav', 'content');
                $('main > div').hide();
                $('#' + targetId).show();

                if (targetId === 'contentProyectosTecnologicos') {
                    // Llama a la función pasando el ID del canvas y el título
                    const proyectosTec = <?php echo json_encode($metas->obtenerProyectosTecPorTipo('Tecnológico')); ?>;
                    generarGraficaBarras('graficaProyectosTec', proyectosTec, 'Proyectos Tecnológicos');
                    generarGraficaTorta('tortasTec', proyectosTec);
                }
                if (targetId === 'contentExtensionismo') {
                    // Llama a la función pasando el ID del canvas y el título
                    const proyectosExt = <?php echo json_encode($metas->obtenerProyectosTecPorTipo('Extensionismo')); ?>;
                    generarGraficaBarras('graficaProyectosExt', proyectosExt, 'Proyectos de Extensionismo');
                    generarGraficaTorta('tortasExt', proyectosExt);
                }
            });

            $('#navProyectosTecnologicos').click();


            let sidebarOpen = true;
            const sidebarFilament = document.getElementById('sidebarFilament');
            const mainContentFilament = document.getElementById('mainContentFilament');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            function openSidebar() {
                sidebarFilament.classList.remove('sidebar-closed');
                mainContentFilament.classList.remove('content-expanded');
                sidebarOverlay.classList.add('active');
                sidebarOpen = true;
                sidebarToggle.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="toggle-icon text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                `;
            }

            function closeSidebar() {
                sidebarFilament.classList.add('sidebar-closed');
                mainContentFilament.classList.add('content-expanded');
                sidebarOverlay.classList.remove('active');
                sidebarOpen = false;
                sidebarToggle.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="toggle-icon text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                `;
            }

            sidebarToggle.addEventListener('click', function() {
                if (sidebarOpen) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });

            sidebarOverlay.addEventListener('click', function() {
                closeSidebar();
            });

            function handleResize() {
                if (window.innerWidth < 1024) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            }

            window.addEventListener('resize', handleResize);
        });
    </script>
    <style>
        .sidebar-link.active {
            background-color: #f0f6ff;
            color: #2563eb;
            border-right: 3px solid #2563eb;
        }

        .dashboard-container {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .stats-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            justify-content: space-around;
        }

        .stat-item {
            text-align: center;
            flex: 1 0 100px;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1.1rem;
            color: #6b7280;
        }

        .tabla-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        }

        .styled-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .styled-table thead tr {
            background-color: #f3f4f6;
            color: #374151;
            text-align: left;
        }

        .styled-table th,
        .styled-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .styled-table tbody tr:nth-of-type(odd) {
            background-color: #f9fafb;
        }

        .styled-table tbody tr:hover {
            background-color: #edf2f7;
            cursor: pointer;
        }

        .styled-table tfoot tr {
            font-weight: bold;
            background-color: #f3f4f6;
            color: #374151;
        }

        .chart-wrapper {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            margin-bottom: 1.5rem;
        }

        .tortas-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            gap: 2rem;
            margin-top: 1rem;
        }

        .torta-card {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            display: inline-block;
            margin-right: 1rem;
            min-width: 220px;
            vertical-align: top;
            position: relative;
        }

        .torta-card .torta-title {
            min-height: 1.5em;
            font-size: 1.1em;
            font-weight: bold;
            margin-bottom: 0.5em;
            text-align: center;
        }

        .torta-card .torta-info {
            font-size: 1em;
            margin-top: 0.5em;
        }

        /* Botón de actualizar tabla */
        .actualizar-tabla-link {
            text-decoration: none;
        }

        .actualizar-tabla-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(90deg, #34d399 0%, #60a5fa 100%);
            color: #fff;
            font-weight: 600;
            font-size: 1.08rem;
            padding: 0.65rem 1.4rem;
            border: none;
            border-radius: 0.7rem;
            box-shadow: 0 2px 8px rgba(52, 211, 153, 0.08), 0 1.5px 6px rgba(96, 165, 250, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.3s;
        }

        .actualizar-tabla-btn:hover {
            background: linear-gradient(90deg, #22c55e 0%, #3b82f6 100%);
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 3px 12px rgba(52, 211, 153, 0.12), 0 2px 8px rgba(96, 165, 250, 0.08);
        }

        .actualizar-tabla-btn:active {
            transform: translateY(0) scale(0.95);
            box-shadow: 0 1px 3px rgba(52, 211, 153, 0.16), 0 0.5px 2px rgba(96, 165, 250, 0.12);
        }
    </style>
</body>
</html>
