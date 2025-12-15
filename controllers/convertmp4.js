const fs = require('fs');
const path = require('path');
const ffmpegPath = require('@ffmpeg-installer/ffmpeg').path;
const ffmpeg = require('fluent-ffmpeg');
ffmpeg.setFfmpegPath(ffmpegPath);

exports.convertToMp4Funct = async (inputFile, outputDir) => {
    const vdofilecv = { fname: [], fcount: [] };
    const outputDirFormat = path.join(outputDir, 'Vdo');

    if (!fs.existsSync(outputDirFormat)) {
        fs.mkdirSync(outputDirFormat, { recursive: true });
    }

    vdofilecv.fcount.push(inputFile.length);

    inputFile.map(item => {
        if (!fs.existsSync(item)) {
            throw new Error(`Input file does not exist: ${item}`);
        }
        vdofilecv.fname.push(item);

        const outputFileName = path.basename(item, path.extname(item)) + ".mp4";
        const outputFilePath = path.join(outputDirFormat, outputFileName);

        ffmpeg(item)
            .output(outputFilePath)
            .on("start", commandLine => console.log("FFmpeg command:", commandLine))
            .on("progress", progress => console.log("Processing", progress))
            .on("end", () => console.log(`Conversion completed: ${outputFilePath}`))
            .on("error", (err, stdout, stderr) => {
                console.error("FFmpeg error:", err.message);
                console.error("FFmpeg stdout:", stdout);
                console.error("FFmpeg stderr:", stderr);
            })
            .run();
    });

    return { vdofilecv, msg: 'Convert vdo success' };
};
