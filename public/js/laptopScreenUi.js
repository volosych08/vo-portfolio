export class LaptopScreenUI {
    constructor(screenCanvas, screenTexture, projects, getProject) {
        this.screenCanvas = screenCanvas;
        this.screenTexture = screenTexture;
        this.context = screenCanvas.getContext('2d');

        this.projects = projects;
        this.getProject = getProject;
        this.activeProject = null;

        this.mode = 'list';

        this.clickableAreas = [];

        this.currentPage = 0;
        this.projectsPerPage = 3;

        this.imageCache = new Map();
        this.loadImages();
    }

    loadImages() {
        this.projects.forEach((project) => {
            if (!project.image) {
                return;
            }

            const image = new Image();
            image.src = project.image;

            image.onload = () => {
                this.imageCache.set(project.image, image);
                this.render();
            };
        });
    }

    render() {
        this.clickableAreas = [];
        this.drawBackground();

        if (this.mode === 'list') {
            this.drawProjectsOverview();
        }

        if (this.mode === 'detail') {
            this.drawProjectDetail();
        }

        this.screenTexture.needsUpdate = true;
    }

    drawBackground() {
        const ctx = this.context;

        const gradient = ctx.createLinearGradient(0, 0, this.screenCanvas.width, this.screenCanvas.height);
        gradient.addColorStop(0, '#a7adb7');
        gradient.addColorStop(0.5, '#7d8490');
        gradient.addColorStop(1, '#a3a8b1');

        ctx.fillStyle = gradient;
        ctx.fillRect(0, 0, this.screenCanvas.width, this.screenCanvas.height);

        ctx.fillStyle = 'rgba(255, 255, 255, 0.07)';
    }

    drawProjectsOverview() {
        this.drawTitle('Projects', 28, 52, 42);

        const totalPages = Math.ceil(this.projects.length / this.projectsPerPage);
        const startIndex = this.currentPage * this.projectsPerPage;
        const visibleProjects = this.projects.slice(startIndex, startIndex + this.projectsPerPage);

        const cardWidth = 300;
        const cardHeight = 405;
        const gap = 24;
        const startX = 24;
        const y = 82;

        visibleProjects.forEach((project, slotIndex) => {
            const realProjectIndex = startIndex + slotIndex;
            const x = startX + slotIndex * (cardWidth + gap);

            this.drawProjectCard(project, realProjectIndex, x, y, cardWidth, cardHeight);
        });

        if (totalPages > 1) {
            this.drawPageControls(totalPages);
        }
    }

    drawPageControls(totalPages) {
        const ctx = this.context;

        const buttonY = 555;

        this.drawButton(
            '← Previous',
            34,
            buttonY,
            145,
            42,
            'rgba(255,255,255,0.16)',
            'previous-page',
            {},
            17
        );

        this.drawButton(
            'Next →',
            845,
            buttonY,
            145,
            42,
            '#2563eb',
            'next-page',
            {},
            17
        );

        ctx.fillStyle = 'rgba(255,255,255,0.9)';
        ctx.font = '18px Arial';

        const pageText = `Page ${this.currentPage + 1} / ${totalPages}`;
        const textWidth = ctx.measureText(pageText).width;

        ctx.fillText(pageText, (this.screenCanvas.width - textWidth) / 2, buttonY + 28);
    }

    drawProjectCard(project, index, x, y, width, height) {
        const ctx = this.context;

        this.roundRect(
            x,
            y,
            width,
            height,
            22,
            'rgba(255, 255, 255, 0.20)',
            'rgba(255, 255, 255, 0.28)'
        );

        const imageX = x + 16;
        const imageY = y + 16;
        const imageWidth = width - 32;
        const imageHeight = 120;

        this.drawImageOrPlaceholder(project.image, imageX, imageY, imageWidth, imageHeight);

        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 22px Arial';
        this.fitText(project.title, x + 18, y + 165, width - 36, 22);

        ctx.fillStyle = 'rgba(255,255,255,0.92)';
        ctx.font = '18px Arial';
        this.wrapText(project.description, x + 18, y + 198, width - 36, 24, 3);

        this.drawStatusPill(project.status, x + 18, y + 304);

        ctx.fillStyle = '#f8fafc';
        ctx.font = '17px Arial';
        ctx.fillText(project.date, x + 18, y + height - 24);

        this.drawButton(
            'View project',
            x + width - 130,
            y + height - 52,
            112,
            38,
            '#2563eb',
            'view-project',
            { projectId: project.id },
            16
        );
    }

    drawProjectDetail() {
        const ctx = this.context;
        const project = this.activeProject;

        if (!project) {
            return;
        }

        const panelX = 22;
        const panelY = 20;
        const panelWidth = 980;
        const panelHeight = 600;

        this.roundRect(
            panelX,
            panelY,
            panelWidth,
            panelHeight,
            24,
            'rgba(255, 255, 255, 0.20)',
            'rgba(255, 255, 255, 0.28)'
        );

        const imageX = panelX + 26;
        const imageY = panelY + 34;
        const imageWidth = 520;
        const imageHeight = 340;

        this.drawImageOrPlaceholder(project.image, imageX, imageY, imageWidth, imageHeight);

        const contentX = imageX + imageWidth + 30;
        const contentY = panelY + 34;
        const contentWidth = 380;

        this.drawStatusPill(project.status, contentX, contentY);

        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 34px Arial';
        this.fitText(project.title, contentX, contentY + 64, contentWidth, 34);

        ctx.fillStyle = 'rgba(255,255,255,0.9)';
        ctx.font = '17px Arial';
        const dateLabel = project.status === 'Completed'
            ? `Completed on ${project.date}`
            : project.status === 'In Progress'
                ? `Started on ${project.date}`
                : `Planned for ${project.date}`;

        ctx.fillText(dateLabel, contentX, contentY + 100);
        ctx.fillText(`Category: ${project.category}`, contentX, contentY + 125);

        this.drawLine(contentX, contentY + 148, contentWidth);

        ctx.fillStyle = '#ffffff';
        ctx.font = '19px Arial';
        this.wrapText(project.longDescription, contentX, contentY + 182, contentWidth, 28, 7);

        this.drawLine(contentX, contentY + 350, contentWidth);

        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 18px Arial';
        ctx.fillText('Tech Stack', contentX, contentY + 380);

        ctx.fillStyle = 'rgba(255,255,255,0.92)';
        ctx.font = '17px Arial';
        this.wrapText(project.tech.join(' • '), contentX, contentY + 410, contentWidth, 24, 3);

        this.drawButton(
            'Back',
            contentX,
            panelY + panelHeight - 62,
            140,
            42,
            'rgba(255,255,255,0.12)',
            'back',
            {},
            18
        );

        this.drawButton(
            'Open',
            contentX + 170,
            panelY + panelHeight - 62,
            150,
            42,
            '#2563eb',
            'open-url',
            {},
            18
        );
    }

    drawTitle(text, x, y, size = 42) {
        const ctx = this.context;

        ctx.fillStyle = '#f8fafc';
        ctx.font = `bold ${size}px Arial`;
        ctx.shadowColor = 'rgba(0, 0, 0, 0.28)';
        ctx.shadowBlur = 6;
        ctx.fillText(text, x, y);
        ctx.shadowBlur = 0;
    }

    drawImageOrPlaceholder(imagePath, x, y, width, height) {
        const ctx = this.context;
        const image = this.imageCache.get(imagePath);

        this.roundRect(
            x,
            y,
            width,
            height,
            16,
            'rgba(15, 23, 42, 0.25)',
            'rgba(255,255,255,0.35)'
        );

        if (image) {
            ctx.save();
            this.clipRoundedRect(x, y, width, height, 16);
            ctx.drawImage(image, x, y, width, height);
            ctx.restore();
            return;
        }

        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 24px Arial';
        ctx.textAlign = 'center';
        ctx.fillText('Project screenshot', x + width / 2, y + height / 2 + 8);
        ctx.textAlign = 'left';
    }

    getStatusStyle(status) {
        const normalizedStatus = String(status).trim().toLowerCase();

        const styles = {
            completed: {
                background: '#22c55e',
                border: 'rgba(255, 255, 255, 0.22)',
                icon: '✓',
            },
            'in progress': {
                background: '#f59e0b',
                border: 'rgba(255, 255, 255, 0.22)',
                icon: '↻',
            },
            planned: {
                background: '#3b82f6',
                border: 'rgba(255, 255, 255, 0.22)',
                icon: '•',
            },
        };

        return styles[normalizedStatus] ?? {
            background: '#64748b',
            border: 'rgba(255, 255, 255, 0.22)',
            icon: '?',
        };
    }

    drawStatusPill(status, x, y) {
        const ctx = this.context;
        const style = this.getStatusStyle(status);

        ctx.font = 'bold 15px Arial';

        const textWidth = ctx.measureText(status).width;
        const pillWidth = textWidth + 58;
        const pillHeight = 30;

        this.roundRect(
            x,
            y,
            pillWidth,
            pillHeight,
            9,
            style.background,
            style.border
        );

        ctx.strokeStyle = '#ffffff';
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.arc(x + 18, y + 15, 8, 0, Math.PI * 2);
        ctx.stroke();

        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 13px Arial';
        ctx.textAlign = 'center';
        ctx.fillText(style.icon, x + 18, y + 20);

        ctx.font = 'bold 15px Arial';
        ctx.textAlign = 'left';
        ctx.fillText(status, x + 36, y + 20);
    }

    drawButton(text, x, y, width, height, color, action, data = {}, fontSize = 20) {
        const ctx = this.context;

        this.roundRect(x, y, width, height, 12, color, 'rgba(255,255,255,0.35)');

        ctx.fillStyle = '#ffffff';
        ctx.font = `bold ${fontSize}px Arial`;

        const textWidth = ctx.measureText(text).width;
        ctx.fillText(text, x + (width - textWidth) / 2, y + height / 2 + fontSize / 3);

        this.addClickableArea(action, x, y, width, height, data);
    }

    drawLine(x, y, width) {
        const ctx = this.context;

        ctx.strokeStyle = 'rgba(255, 255, 255, 0.25)';
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(x, y);
        ctx.lineTo(x + width, y);
        ctx.stroke();
    }

    wrapText(text, x, y, maxWidth, lineHeight, maxLines = 10) {
        const ctx = this.context;
        const words = text.split(' ');
        let line = '';
        let lines = 0;

        for (const word of words) {
            const testLine = line + word + ' ';
            const width = ctx.measureText(testLine).width;

            if (width > maxWidth) {
                ctx.fillText(line.trim(), x, y);
                line = word + ' ';
                y += lineHeight;
                lines++;

                if (lines >= maxLines) {
                    return;
                }
            } else {
                line = testLine;
            }
        }

        if (lines < maxLines && line.trim() !== '') {
            ctx.fillText(line.trim(), x, y);
        }
    }

    fitText(text, x, y, maxWidth, fontSize) {
        const ctx = this.context;
        let output = text;

        while (ctx.measureText(output).width > maxWidth && output.length > 3) {
            output = output.slice(0, -1);
        }

        if (output !== text) {
            output = output.slice(0, -3) + '...';
        }

        ctx.fillText(output, x, y);
    }

    addClickableArea(id, x, y, width, height, data = {}) {
        this.clickableAreas.push({
            id,
            x,
            y,
            width,
            height,
            data,
        });
    }

    async handleClick(x, y) {
        for (const area of this.clickableAreas) {
            const insideX = x >= area.x && x <= area.x + area.width;
            const insideY = y >= area.y && y <= area.y + area.height;

            if (!insideX || !insideY) {
                continue;
            }

            if (area.id === 'view-project') {
                try {
                    this.activeProject = await this.getProject(area.data.projectId);
                    this.mode = 'detail';
                    this.render();
                } catch (error) {
                    console.error('Could not load project details:', error);
                }

                return;
            }

            if (area.id === 'previous-page') {
                const totalPages = Math.ceil(this.projects.length / this.projectsPerPage);

                this.currentPage -= 1;

                if (this.currentPage < 0) {
                    this.currentPage = totalPages - 1;
                }

                this.render();
                return;
            }

            if (area.id === 'next-page') {
                const totalPages = Math.ceil(this.projects.length / this.projectsPerPage);

                this.currentPage += 1;

                if (this.currentPage >= totalPages) {
                    this.currentPage = 0;
                }

                this.render();
                return;
            }

            if (area.id === 'back') {
                this.mode = 'list';
                this.render();
                return;
            }

            if (area.id === 'open-url' && this.activeProject) {
                window.location.href = this.activeProject.url;
                return;
            }
        }
    }

    roundRect(x, y, width, height, radius, fill, stroke = null) {
        const ctx = this.context;

        ctx.beginPath();
        ctx.moveTo(x + radius, y);
        ctx.lineTo(x + width - radius, y);
        ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
        ctx.lineTo(x + width, y + height - radius);
        ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
        ctx.lineTo(x + radius, y + height);
        ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
        ctx.lineTo(x, y + radius);
        ctx.quadraticCurveTo(x, y, x + radius, y);
        ctx.closePath();

        ctx.fillStyle = fill;
        ctx.fill();

        if (stroke) {
            ctx.strokeStyle = stroke;
            ctx.lineWidth = 2;
            ctx.stroke();
        }
    }

    clipRoundedRect(x, y, width, height, radius) {
        const ctx = this.context;

        ctx.beginPath();
        ctx.moveTo(x + radius, y);
        ctx.lineTo(x + width - radius, y);
        ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
        ctx.lineTo(x + width, y + height - radius);
        ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
        ctx.lineTo(x + radius, y + height);
        ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
        ctx.lineTo(x, y + radius);
        ctx.quadraticCurveTo(x, y, x + radius, y);
        ctx.closePath();
        ctx.clip();
    }
}