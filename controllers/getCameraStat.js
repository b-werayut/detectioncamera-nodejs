const { readdirSync } = require('fs');
const path = require('path');

exports.getCameraStat = async (req, res) => {
    try {
        const directoryPath = path.resolve('../detectionstreaming/');
        const cameraStat = [];

        const items = readdirSync(directoryPath, { withFileTypes: true });
        const folders = items.filter(item => item.isDirectory());
        const folderNames = folders.map(folder => folder.name);

        folderNames.forEach((folderName) => {
            const subFolder = path.resolve(directoryPath, folderName);
            const subFolderItems = readdirSync(subFolder, { withFileTypes: true });

            const tsFiles = subFolderItems.filter(item =>
                item.isFile() && item.name.toLowerCase().endsWith('.ts')
            );

            let status = 1;
            if (tsFiles.length === 0 || tsFiles.length <= 3) {
                status = 0;
            }

            cameraStat.push({
                camera: folderName,
                tsFileCount: tsFiles.length,
                status: Number(status)
            });
        });

        res.json({ msg: "Success", cameraStat });

    } catch (err) {
        console.error('Error reading directory:', err);
        res.status(500).json({ msg: "Error reading directory", error: err.message });
    }
};
