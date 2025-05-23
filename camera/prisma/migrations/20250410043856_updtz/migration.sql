BEGIN TRY

BEGIN TRAN;

-- AlterTable
ALTER TABLE [dbo].[TmstCameraDetectionLogs] ALTER COLUMN [createdAt] NVARCHAR(1000) NULL;
ALTER TABLE [dbo].[TmstCameraDetectionLogs] ALTER COLUMN [updatedAt] NVARCHAR(1000) NULL;
ALTER TABLE [dbo].[TmstCameraDetectionLogs] ADD CONSTRAINT [TmstCameraDetectionLogs_updatedAt_df] DEFAULT 'No Logs Update' FOR [updatedAt];

COMMIT TRAN;

END TRY
BEGIN CATCH

IF @@TRANCOUNT > 0
BEGIN
    ROLLBACK TRAN;
END;
THROW

END CATCH
