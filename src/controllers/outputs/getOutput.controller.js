const { db } = require('../../config/database');

exports.getOutputController = async (req, res) => {
    try {
        const { noId } = req.params;

        const [rows] = await db
        .promise()
        // .query("SELECT images FROM outputs WHERE noId = ?", [noId]);
        .query("SELECT images FROM lclm_outputs WHERE noId = ?", [noId]);

        if(rows.length === 0) {
            return res.status(200).json({
                statusCode: 200,
                taskStatus: false,
                message: `ไม่พบข้อมูลรูปภาพ จาก ID ${noId}`,
            });
        }
        const data = rows[0];

        const baseUrl = `${req.protocol}://${req.get("host")}/uploads`;
        const imageUrls = rows.map(row => ({
            image: `${baseUrl}/${row.images}`
        }));

        const output = {
            id: data.id,
            image: imageUrls,
            noId: data.noId,
            created_at: data.created_at,
        }

        return res.status(200).json({
            statusCode: 200,
            taskStatus: true,
            message: "พบข้อมูลรูปภาพ",
            data: output,
        });
    } catch (error) {
        console.error("Error: ", error)
        res
         .status(500)
         .json({ statusCode: 500, taskStatus: false, message: error.message });
    }
};